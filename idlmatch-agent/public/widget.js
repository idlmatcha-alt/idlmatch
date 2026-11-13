(function() {
  'use strict';

  // ── Config ──────────────────────────────────────────
  const CONFIG = {
    serverUrl: window.location.origin,
    agentName: 'Noor',
    agentTitle: 'IDLMatch Assistant',
    agentAvatar: '🌙',
    primaryColor: '#c8a84b',
    primaryGradient: 'linear-gradient(135deg, #2d1b4e, #4a2c6e)',
    headerGradient: 'linear-gradient(135deg, #2d1b4e 0%, #3d2460 50%, #2a4a2a 100%)',
    bubbleColor: 'linear-gradient(135deg, #2d1b4e, #4a2c6e)',
    userBubbleColor: 'linear-gradient(135deg, #c8a84b, #d4af37)',
    whatsappLink: 'https://wa.me/923122294445',
    sessionKey: 'idlmatch_session_id',
    welcomeMessage: "Assalamualaikum! 🌟 Welcome to IDLMatch",
  };

  let sessionId = localStorage.getItem(CONFIG.sessionKey);
  let socket = null;
  let isOpen = false;
  let isTyping = false;
  let unreadCount = 0;

  // ── Inject Styles ────────────────────────────────────
  const style = document.createElement('style');
  style.textContent = `
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    #idlmatch-widget * { box-sizing: border-box; font-family: 'Inter', sans-serif; }

    #idlmatch-bubble {
      position: fixed; bottom: 24px; right: 24px; z-index: 999999;
      width: 62px; height: 62px; border-radius: 50%;
      background: ${CONFIG.bubbleColor};
      border: none; cursor: pointer;
      box-shadow: 0 8px 32px rgba(200,168,75,0.4), 0 2px 8px rgba(0,0,0,0.3);
      display: flex; align-items: center; justify-content: center;
      font-size: 26px; transition: all 0.3s cubic-bezier(0.34,1.56,0.64,1);
      animation: idlm-pulse 2s infinite;
    }
    #idlmatch-bubble:hover { transform: scale(1.1); box-shadow: 0 12px 40px rgba(200,168,75,0.6); }
    #idlmatch-bubble.open { animation: none; transform: scale(0.9); }

    @keyframes idlm-pulse {
      0%, 100% { box-shadow: 0 8px 32px rgba(200,168,75,0.4), 0 0 0 0 rgba(200,168,75,0.4); }
      50% { box-shadow: 0 8px 32px rgba(200,168,75,0.4), 0 0 0 10px rgba(200,168,75,0); }
    }

    #idlmatch-badge {
      position: absolute; top: -4px; right: -4px;
      background: #e74c3c; color: white; border-radius: 50%;
      width: 20px; height: 20px; font-size: 11px; font-weight: 700;
      display: flex; align-items: center; justify-content: center;
      display: none; border: 2px solid white;
    }

    #idlmatch-window {
      position: fixed; bottom: 100px; right: 24px; z-index: 999998;
      width: 380px; height: 580px; border-radius: 20px;
      background: #0f0a1a; overflow: hidden;
      box-shadow: 0 20px 60px rgba(0,0,0,0.6), 0 0 0 1px rgba(200,168,75,0.2);
      display: flex; flex-direction: column;
      transform: scale(0.8) translateY(20px); opacity: 0;
      transition: all 0.4s cubic-bezier(0.34,1.56,0.64,1);
      pointer-events: none;
    }
    #idlmatch-window.open {
      transform: scale(1) translateY(0); opacity: 1; pointer-events: all;
    }

    #idlmatch-header {
      background: ${CONFIG.headerGradient};
      padding: 16px 18px; display: flex; align-items: center; gap: 12px;
      border-bottom: 1px solid rgba(200,168,75,0.2);
      flex-shrink: 0;
    }
    #idlmatch-avatar {
      width: 44px; height: 44px; border-radius: 50%;
      background: rgba(200,168,75,0.2);
      border: 2px solid rgba(200,168,75,0.5);
      display: flex; align-items: center; justify-content: center;
      font-size: 22px; flex-shrink: 0;
    }
    #idlmatch-header-info { flex: 1; }
    #idlmatch-header-name { color: #d4af37; font-weight: 700; font-size: 15px; }
    #idlmatch-header-status {
      color: rgba(255,255,255,0.6); font-size: 12px; margin-top: 2px;
      display: flex; align-items: center; gap: 5px;
    }
    #idlmatch-status-dot {
      width: 7px; height: 7px; border-radius: 50%;
      background: #2ecc71; display: inline-block;
      animation: idlm-blink 2s infinite;
    }
    @keyframes idlm-blink {
      0%, 100% { opacity: 1; } 50% { opacity: 0.3; }
    }
    #idlmatch-close {
      background: rgba(255,255,255,0.1); border: none; cursor: pointer;
      width: 32px; height: 32px; border-radius: 50%;
      color: rgba(255,255,255,0.7); font-size: 18px;
      display: flex; align-items: center; justify-content: center;
      transition: all 0.2s; flex-shrink: 0;
    }
    #idlmatch-close:hover { background: rgba(255,255,255,0.2); color: white; }

    #idlmatch-wa-btn {
      background: rgba(37,211,102,0.15); border: 1px solid rgba(37,211,102,0.4);
      color: #25d366; border-radius: 8px; padding: 4px 10px;
      font-size: 11px; cursor: pointer; white-space: nowrap;
      text-decoration: none; display: inline-flex; align-items: center; gap: 4px;
      transition: all 0.2s;
    }
    #idlmatch-wa-btn:hover { background: rgba(37,211,102,0.3); }

    #idlmatch-messages {
      flex: 1; overflow-y: auto; padding: 16px;
      display: flex; flex-direction: column; gap: 12px;
      scrollbar-width: thin; scrollbar-color: rgba(200,168,75,0.3) transparent;
    }
    #idlmatch-messages::-webkit-scrollbar { width: 4px; }
    #idlmatch-messages::-webkit-scrollbar-thumb { background: rgba(200,168,75,0.3); border-radius: 2px; }

    .idlm-msg { display: flex; gap: 8px; max-width: 85%; animation: idlm-fadeIn 0.3s ease; }
    .idlm-msg.user { margin-left: auto; flex-direction: row-reverse; }
    @keyframes idlm-fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .idlm-msg-avatar {
      width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
      display: flex; align-items: center; justify-content: center;
      font-size: 15px; background: rgba(200,168,75,0.15);
      border: 1px solid rgba(200,168,75,0.3); align-self: flex-end;
    }

    .idlm-bubble {
      padding: 11px 15px; border-radius: 18px; font-size: 13.5px;
      line-height: 1.5; word-wrap: break-word;
    }
    .idlm-msg.agent .idlm-bubble {
      background: rgba(255,255,255,0.07);
      border: 1px solid rgba(200,168,75,0.15);
      color: #e8e8e8; border-bottom-left-radius: 4px;
    }
    .idlm-msg.user .idlm-bubble {
      background: ${CONFIG.userBubbleColor};
      color: #1a0a0a; font-weight: 500; border-bottom-right-radius: 4px;
    }
    .idlm-time {
      font-size: 10px; color: rgba(255,255,255,0.3);
      margin-top: 4px; text-align: right;
    }
    .idlm-msg.agent .idlm-time { text-align: left; }

    #idlmatch-typing {
      display: none; align-items: center; gap: 8px; padding: 0 16px 8px;
    }
    #idlmatch-typing .idlm-msg-avatar { width: 28px; height: 28px; font-size: 13px; }
    .idlm-typing-dots { display: flex; gap: 4px; padding: 10px 14px; background: rgba(255,255,255,0.07); border-radius: 16px; }
    .idlm-dot { width: 7px; height: 7px; border-radius: 50%; background: #c8a84b; animation: idlm-bounce 1.2s infinite; }
    .idlm-dot:nth-child(2) { animation-delay: 0.2s; }
    .idlm-dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes idlm-bounce { 0%, 60%, 100% { transform: translateY(0); } 30% { transform: translateY(-8px); } }

    #idlmatch-input-area {
      padding: 12px 14px; border-top: 1px solid rgba(200,168,75,0.15);
      background: rgba(255,255,255,0.03); flex-shrink: 0;
    }
    #idlmatch-input-row { display: flex; gap: 8px; align-items: flex-end; }
    #idlmatch-input {
      flex: 1; background: rgba(255,255,255,0.08);
      border: 1px solid rgba(200,168,75,0.25); border-radius: 24px;
      padding: 10px 16px; color: white; font-size: 13.5px;
      outline: none; resize: none; max-height: 100px;
      font-family: 'Inter', sans-serif; line-height: 1.4;
      transition: border-color 0.2s;
    }
    #idlmatch-input::placeholder { color: rgba(255,255,255,0.35); }
    #idlmatch-input:focus { border-color: rgba(200,168,75,0.6); }
    #idlmatch-send {
      width: 42px; height: 42px; border-radius: 50%; border: none;
      background: ${CONFIG.primaryGradient}; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      transition: all 0.2s; flex-shrink: 0;
      box-shadow: 0 4px 15px rgba(200,168,75,0.3);
    }
    #idlmatch-send:hover { transform: scale(1.05); box-shadow: 0 6px 20px rgba(200,168,75,0.5); }
    #idlmatch-send svg { width: 18px; height: 18px; fill: none; stroke: white; stroke-width: 2; }

    #idlmatch-footer {
      text-align: center; padding: 6px; font-size: 10px;
      color: rgba(255,255,255,0.2); background: rgba(0,0,0,0.2);
      flex-shrink: 0;
    }
    #idlmatch-footer a { color: rgba(200,168,75,0.4); text-decoration: none; }

    @media (max-width: 440px) {
      #idlmatch-window { width: calc(100vw - 20px); right: 10px; bottom: 90px; height: 70vh; }
    }
  `;
  document.head.appendChild(style);

  // ── Build HTML ───────────────────────────────────────
  const widget = document.createElement('div');
  widget.id = 'idlmatch-widget';
  widget.innerHTML = `
    <div id="idlmatch-bubble">
      🌙
      <div id="idlmatch-badge">0</div>
    </div>
    <div id="idlmatch-window">
      <div id="idlmatch-header">
        <div id="idlmatch-avatar">🌙</div>
        <div id="idlmatch-header-info">
          <div id="idlmatch-header-name">Noor · IDLMatch Assistant</div>
          <div id="idlmatch-header-status">
            <span id="idlmatch-status-dot"></span>
            <span id="idlmatch-status-text">Online · Islamic Matrimonial</span>
          </div>
        </div>
        <a id="idlmatch-wa-btn" href="${CONFIG.whatsappLink}" target="_blank">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M11.999 2C6.477 2 2 6.477 2 12c0 1.994.573 3.886 1.57 5.5L2 22l4.635-1.54A9.956 9.956 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2z"/></svg>
          WhatsApp
        </a>
        <button id="idlmatch-close">✕</button>
      </div>
      <div id="idlmatch-messages"></div>
      <div id="idlmatch-typing">
        <div class="idlm-msg-avatar">🌙</div>
        <div class="idlm-typing-dots">
          <div class="idlm-dot"></div>
          <div class="idlm-dot"></div>
          <div class="idlm-dot"></div>
        </div>
      </div>
      <div id="idlmatch-input-area">
        <div id="idlmatch-input-row">
          <textarea id="idlmatch-input" placeholder="Type your message... / اپنا پیغام لکھیں" rows="1"></textarea>
          <button id="idlmatch-send">
            <svg viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
          </button>
        </div>
      </div>
      <div id="idlmatch-footer">Powered by <a href="https://idlmatch.com" target="_blank">IDLMatch.com</a></div>
    </div>
  `;
  document.body.appendChild(widget);

  // ── Elements ─────────────────────────────────────────
  const bubble = document.getElementById('idlmatch-bubble');
  const win = document.getElementById('idlmatch-window');
  const messages = document.getElementById('idlmatch-messages');
  const input = document.getElementById('idlmatch-input');
  const sendBtn = document.getElementById('idlmatch-send');
  const closeBtn = document.getElementById('idlmatch-close');
  const typingEl = document.getElementById('idlmatch-typing');
  const badge = document.getElementById('idlmatch-badge');

  // ── Socket ───────────────────────────────────────────
  function loadSocket() {
    const script = document.createElement('script');
    script.src = CONFIG.serverUrl + '/socket.io/socket.io.js';
    script.onload = initSocket;
    document.head.appendChild(script);
  }

  function initSocket() {
    socket = io(CONFIG.serverUrl, { transports: ['websocket', 'polling'] });

    socket.on('connect', () => {
      socket.emit('visitor_start', { sessionId });
    });

    socket.on('session_id', (data) => {
      sessionId = data.sessionId;
      localStorage.setItem(CONFIG.sessionKey, sessionId);
    });

    socket.on('history', (history) => {
      history.forEach(msg => appendMessage(msg.role, msg.content, msg.timestamp, false));
      scrollToBottom();
    });

    socket.on('message', (data) => {
      appendMessage(data.role === 'agent' ? 'agent' : 'user', data.content, data.timestamp);
      if (!isOpen) showBadge();
    });

    socket.on('typing', (data) => {
      typingEl.style.display = data.typing ? 'flex' : 'none';
      if (data.typing) scrollToBottom();
    });

    socket.on('agent_status', (data) => {
      appendSystemMessage(data.message);
    });
  }

  // ── UI ────────────────────────────────────────────────
  function toggleWidget() {
    isOpen = !isOpen;
    win.classList.toggle('open', isOpen);
    bubble.classList.toggle('open', isOpen);
    if (isOpen) {
      clearBadge();
      setTimeout(() => input.focus(), 300);
      scrollToBottom();
      if (!socket) loadSocket();
    }
  }

  function appendMessage(role, content, timestamp, animate = true) {
    const div = document.createElement('div');
    div.className = `idlm-msg ${role === 'user' ? 'user' : 'agent'}`;
    const time = timestamp ? new Date(timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
    div.innerHTML = `
      ${role !== 'user' ? `<div class="idlm-msg-avatar">🌙</div>` : ''}
      <div>
        <div class="idlm-bubble">${escapeHtml(content).replace(/\n/g, '<br>').replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" style="color:#d4af37">$1</a>')}</div>
        <div class="idlm-time">${time}</div>
      </div>
    `;
    messages.appendChild(div);
    scrollToBottom();
  }

  function appendSystemMessage(text) {
    const div = document.createElement('div');
    div.style.cssText = 'text-align:center;font-size:11px;color:rgba(255,255,255,0.4);padding:4px 0;';
    div.textContent = `— ${text} —`;
    messages.appendChild(div);
    scrollToBottom();
  }

  function sendMessage() {
    const text = input.value.trim();
    if (!text || !socket) return;
    appendMessage('user', text, new Date().toISOString());
    socket.emit('visitor_message', { sessionId, message: text });
    input.value = '';
    input.style.height = 'auto';
  }

  function scrollToBottom() {
    setTimeout(() => messages.scrollTop = messages.scrollHeight, 50);
  }

  function showBadge() {
    unreadCount++;
    badge.textContent = unreadCount;
    badge.style.display = 'flex';
  }

  function clearBadge() {
    unreadCount = 0;
    badge.style.display = 'none';
  }

  function escapeHtml(text) {
    return text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }

  // ── Events ───────────────────────────────────────────
  bubble.addEventListener('click', toggleWidget);
  closeBtn.addEventListener('click', (e) => { e.stopPropagation(); toggleWidget(); });

  sendBtn.addEventListener('click', sendMessage);
  input.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
  });
  input.addEventListener('input', () => {
    input.style.height = 'auto';
    input.style.height = Math.min(input.scrollHeight, 100) + 'px';
  });

  // Auto-open after 3 seconds
  setTimeout(() => { if (!isOpen) { toggleWidget(); } }, 3000);

})();
