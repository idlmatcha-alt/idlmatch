const { JSONFileSync, LowSync } = require('lowdb/node');
const { join } = require('path');
const { v4: uuidv4 } = require('uuid');

const adapter = new JSONFileSync(join(__dirname, 'chatdata.json'));
const db = new LowSync(adapter, {
  sessions: [],
  messages: [],
  leads: []
});

function initDB() {
  db.read();
  db.write();
  console.log('✅ Database (JSON) initialized');
  return db;
}

function getDB() { return db; }

// ── Helper methods mirroring SQL API ─────────────────
const dbHelpers = {
  // Sessions
  getSession: (id) => { db.read(); return db.data.sessions.find(s => s.id === id); },
  createSession: (id) => {
    db.read();
    db.data.sessions.push({ id, visitor_name: null, lead_email: null, lead_phone: null, country: null, started_at: new Date().toISOString(), last_active: new Date().toISOString(), is_escalated: 0, is_human_taking_over: 0 });
    db.write();
  },
  updateSession: (id, fields) => {
    db.read();
    const idx = db.data.sessions.findIndex(s => s.id === id);
    if(idx > -1) { Object.assign(db.data.sessions[idx], fields); db.write(); }
  },
  getSessions: () => {
    db.read();
    return db.data.sessions.map(s => {
      const msgs = db.data.messages.filter(m => m.session_id === s.id);
      return { ...s, message_count: msgs.length, last_message: msgs.length ? msgs[msgs.length-1].content : null };
    }).sort((a,b) => new Date(b.last_active) - new Date(a.last_active));
  },

  // Messages
  addMessage: (session_id, role, content) => {
    db.read();
    db.data.messages.push({ id: db.data.messages.length+1, session_id, role, content, timestamp: new Date().toISOString() });
    db.write();
  },
  getMessages: (session_id) => {
    db.read();
    return db.data.messages.filter(m => m.session_id === session_id).sort((a,b)=>a.id-b.id);
  },

  // Leads
  addLead: (session_id, name, email, phone, country, notes='') => {
    db.read();
    db.data.leads.push({ id: db.data.leads.length+1, session_id, name, email, phone, country, notes, created_at: new Date().toISOString() });
    db.write();
  },
  getLeads: () => { db.read(); return [...db.data.leads].sort((a,b)=>new Date(b.created_at)-new Date(a.created_at)); },

  // Stats
  getStats: () => {
    db.read();
    const today = new Date().toISOString().split('T')[0];
    return {
      totalSessions: db.data.sessions.length,
      totalLeads: db.data.leads.length,
      escalated: db.data.sessions.filter(s=>s.is_escalated).length,
      todaySessions: db.data.sessions.filter(s=>s.started_at && s.started_at.startsWith(today)).length
    };
  }
};

module.exports = { initDB, getDB, dbHelpers };
