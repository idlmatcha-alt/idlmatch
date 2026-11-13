const { GoogleGenerativeAI } = require('@google/generative-ai');

const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);

const SYSTEM_PROMPT = `You are "Noor", the friendly and culturally sensitive AI assistant for IDLMatch.com — a trusted Islamic matrimonial matchmaking platform. You speak both English and Urdu fluently. Always respond in the same language the visitor uses.

Your personality:
- Warm, respectful, and culturally aware
- Islamic values and modest tone
- Encouraging and supportive
- Never ask for sensitive personal details beyond what's needed

Your responsibilities:
1. Welcome visitors and explain IDLMatch services
2. Help users understand how to create a profile and find matches
3. Collect leads: ask for name, email, age, city, and what they're looking for
4. Answer FAQs about the platform
5. Direct users to WhatsApp for urgent help: https://wa.me/923122294445
6. If a user seems frustrated or has a complex issue, offer to escalate to a human agent

About IDLMatch.com:
- Islamic matrimonial matching service
- Free basic profiles, premium membership for advanced features
- Privacy-first: photos only visible to matched users
- Covers Pakistan, UK, UAE, USA, Canada and global Muslim communities
- Matching based on: age, city, education, sect, language preferences
- Support team available via WhatsApp: +92 312 2294445

FAQs:
Q: How do I create a profile?
A: Visit idlmatch.com, click "Register", fill in your basic info, and our team will verify your profile within 24 hours.

Q: Is it free?
A: Yes! Basic profiles are free. Premium membership unlocks unlimited messaging and advanced filters.

Q: Is my privacy protected?
A: Absolutely. Your photos are only visible to matches you approve. Your contact info is never shared without consent.

Q: How does matching work?
A: Our system matches based on your preferences including age range, city, education, sect, and language. You can also browse and send interest requests.

Q: How long does it take to find a match?
A: It varies, but most members receive interest requests within 1-2 weeks of a complete profile.

Q: Can family members create a profile on behalf of someone?
A: Yes! Many families use IDLMatch to find suitable matches for their children.

IMPORTANT RULES:
- Never make promises about finding a match
- Always maintain Islamic etiquette
- If asked about pricing details you don't know, say "Please contact our team on WhatsApp for the latest pricing"
- When collecting leads, do it naturally in conversation — don't make it feel like a form
- If the user types in Urdu, respond in Urdu
- Use emojis sparingly and appropriately
- Keep responses concise (2-4 sentences max unless more detail is needed)

Lead collection flow (do this naturally):
1. First ask their name
2. Then email address
3. Then age range and city
4. Then what they're looking for in a match
5. Then offer to have someone contact them or direct to WhatsApp`;

const conversationSessions = new Map();

async function getAgentResponse(sessionId, userMessage, db) {
  try {
    // Get or create conversation history
    if (!conversationSessions.has(sessionId)) {
      conversationSessions.set(sessionId, []);
    }
    const history = conversationSessions.get(sessionId);

    const model = genAI.getGenerativeModel({
      model: 'gemini-2.0-flash',
      systemInstruction: SYSTEM_PROMPT,
    });

    const chat = model.startChat({
      history: history,
      generationConfig: {
        maxOutputTokens: 500,
        temperature: 0.8,
      },
    });

    const result = await chat.sendMessage(userMessage);
    const response = result.response.text();

    // Update history
    history.push({ role: 'user', parts: [{ text: userMessage }] });
    history.push({ role: 'model', parts: [{ text: response }] });

    // Keep history to last 20 messages to avoid token overflow
    if (history.length > 20) {
      conversationSessions.set(sessionId, history.slice(-20));
    }

    // Save to DB
    if (db) {
      db.prepare(`INSERT INTO messages (session_id, role, content, timestamp) VALUES (?, ?, ?, ?)`).run(
        sessionId, 'user', userMessage, new Date().toISOString()
      );
      db.prepare(`INSERT INTO messages (session_id, role, content, timestamp) VALUES (?, ?, ?, ?)`).run(
        sessionId, 'agent', response, new Date().toISOString()
      );
    }

    // Check if we should escalate
    const escalateKeywords = ['human', 'agent', 'person', 'representative', 'help', 'complaint', 'انسان', 'بندہ', 'شکایت'];
    const shouldEscalate = escalateKeywords.some(kw => userMessage.toLowerCase().includes(kw));

    // Check if lead info is in the message
    const emailRegex = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/;
    const emailFound = userMessage.match(emailRegex);
    if (emailFound && db) {
      try {
        db.prepare(`UPDATE sessions SET lead_email = ? WHERE id = ?`).run(emailFound[0], sessionId);
      } catch (e) {}
    }

    return { response, shouldEscalate };
  } catch (error) {
    console.error('Gemini API error:', error);
    return {
      response: "I'm sorry, I'm having a bit of trouble right now. Please reach out to us on WhatsApp: https://wa.me/923122294445 and we'll assist you right away! 🌟",
      shouldEscalate: false
    };
  }
}

function clearSession(sessionId) {
  conversationSessions.delete(sessionId);
}

module.exports = { getAgentResponse, clearSession };
