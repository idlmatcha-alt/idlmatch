require('dotenv').config();
const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');
const path = require('path');
const { v4: uuidv4 } = require('uuid');
const { initDB, dbHelpers: db } = require('./db');
const { getAgentResponse } = require('./agent');

const app = express();
const server = http.createServer(app);
const io = new Server(server, { cors: { origin: '*', methods: ['GET', 'POST'] } });

app.use(cors());
app.use(express.json());
app.use(express.static(path.join(__dirname, 'public')));

initDB();

function adminAuth(req, res, next) {
  const token = req.headers['x-admin-token'] || req.query.token;
  if (token === process.env.ADMIN_PASSWORD) return next();
  res.status(401).json({ error: 'Unauthorized' });
}

// ── ADMIN ROUTES ──────────────────────────────────────
app.post('/api/admin/login', (req, res) => {
  if (req.body.password === process.env.ADMIN_PASSWORD)
    res.json({ success: true, token: process.env.ADMIN_PASSWORD });
  else res.status(401).json({ success: false, message: 'Invalid password' });
});

app.get('/api/admin/sessions', adminAuth, (req, res) => res.json(db.getSessions()));

app.get('/api/admin/sessions/:id/messages', adminAuth, (req, res) =>
  res.json(db.getMessages(req.params.id)));

app.get('/api/admin/leads', adminAuth, (req, res) => res.json(db.getLeads()));

app.post('/api/admin/leads', adminAuth, (req, res) => {
  const { session_id, name, email, phone, country, notes } = req.body;
  db.addLead(session_id, name, email, phone, country, notes);
  res.json({ success: true });
});

app.post('/api/admin/sessions/:id/takeover', adminAuth, (req, res) => {
  db.updateSession(req.params.id, { is_human_taking_over: 1 });
  io.to(req.params.id).emit('agent_status', { type: 'human_takeover', message: 'You are now connected with a human agent.' });
  res.json({ success: true });
});

app.post('/api/admin/sessions/:id/release', adminAuth, (req, res) => {
  db.updateSession(req.params.id, { is_human_taking_over: 0 });
  io.to(req.params.id).emit('agent_status', { type: 'ai_resumed', message: 'You are back with Noor, our AI assistant.' });
  res.json({ success: true });
});

app.post('/api/admin/reply/:sessionId', adminAuth, (req, res) => {
  const { message } = req.body;
  const { sessionId } = req.params;
  db.addMessage(sessionId, 'human_agent', message);
  io.to(sessionId).emit('message', { role: 'agent', content: message, timestamp: new Date().toISOString() });
  io.to('admin_room').emit('admin_new_message', { sessionId, role: 'human_agent', content: message });
  res.json({ success: true });
});

app.get('/api/admin/stats', adminAuth, (req, res) => res.json(db.getStats()));

// ── SOCKET.IO ─────────────────────────────────────────
io.on('connection', (socket) => {
  console.log('🔌 Connected:', socket.id);

  socket.on('admin_join', (data) => {
    if (data.token === process.env.ADMIN_PASSWORD) {
      socket.join('admin_room');
      console.log('👑 Admin connected');
    }
  });

  socket.on('visitor_start', (data) => {
    const sessionId = data.sessionId || uuidv4();
    socket.join(sessionId);
    socket.sessionId = sessionId;

    const existing = db.getSession(sessionId);
    if (!existing) {
      db.createSession(sessionId);
      const welcome = "Assalamualaikum! 🌟 Welcome to IDLMatch.com — your trusted Islamic matrimonial platform. I'm Noor, your personal assistant. How can I help you today? Are you looking to create a profile or find a suitable match?";
      socket.emit('message', { role: 'agent', content: welcome, timestamp: new Date().toISOString() });
      db.addMessage(sessionId, 'agent', welcome);
    } else {
      const history = db.getMessages(sessionId);
      socket.emit('history', history);
    }

    socket.emit('session_id', { sessionId });
    io.to('admin_room').emit('admin_session_update', { type: 'new_visitor', sessionId });
  });

  socket.on('visitor_message', async (data) => {
    const { sessionId, message } = data;
    if (!sessionId || !message) return;

    db.updateSession(sessionId, { last_active: new Date().toISOString() });
    io.to('admin_room').emit('admin_new_message', { sessionId, role: 'user', content: message, timestamp: new Date().toISOString() });

    const session = db.getSession(sessionId);
    if (session && session.is_human_taking_over) {
      db.addMessage(sessionId, 'user', message);
      io.to('admin_room').emit('admin_needs_reply', { sessionId, message });
      return;
    }

    db.addMessage(sessionId, 'user', message);
    socket.emit('typing', { typing: true });

    const { response, shouldEscalate } = await getAgentResponse(sessionId, message, db);
    socket.emit('typing', { typing: false });
    socket.emit('message', { role: 'agent', content: response, timestamp: new Date().toISOString() });
    db.addMessage(sessionId, 'agent', response);
    io.to('admin_room').emit('admin_new_message', { sessionId, role: 'agent', content: response, timestamp: new Date().toISOString() });

    // Auto-capture email from message
    const emailMatch = message.match(/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/);
    if (emailMatch) db.updateSession(sessionId, { lead_email: emailMatch[0] });

    if (shouldEscalate) {
      db.updateSession(sessionId, { is_escalated: 1 });
      io.to('admin_room').emit('admin_escalation', { sessionId });
    }
  });

  socket.on('visitor_lead', (data) => {
    const { sessionId, name, email, phone, country } = data;
    db.updateSession(sessionId, { visitor_name: name, lead_email: email, lead_phone: phone, country });
    db.addLead(sessionId, name, email, phone, country);
    io.to('admin_room').emit('admin_new_lead', { sessionId, name, email });
  });

  socket.on('disconnect', () => console.log('❌ Disconnected:', socket.id));
});

// ── STATIC ────────────────────────────────────────────
app.get('/', (req, res) => res.sendFile(path.join(__dirname, 'public', 'index.html')));
app.get('/admin', (req, res) => res.sendFile(path.join(__dirname, 'public', 'admin.html')));
app.get('/widget.js', (req, res) => res.sendFile(path.join(__dirname, 'public', 'widget.js')));

const PORT = process.env.PORT || 3001;
server.listen(PORT, () => {
  console.log(`\n🌟 IDLMatch Chat Agent  → http://localhost:${PORT}`);
  console.log(`📊 Admin Dashboard     → http://localhost:${PORT}/admin`);
  console.log(`🔑 Admin Password: ${process.env.ADMIN_PASSWORD}\n`);
});
