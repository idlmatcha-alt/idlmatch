(function(){
  const css='support/assets/widget.css';
  if(!document.querySelector('link[data-idmatch-support]')){const l=document.createElement('link');l.rel='stylesheet';l.href='/support/assets/widget.css';l.dataset.idmatchSupport='1';document.head.appendChild(l)}
  function mount(){
    if(document.querySelector('.idmatch-ai-widget'))return;
    const root=document.createElement('div');root.className='idmatch-ai-widget';root.innerHTML=`
      <button class="idmatch-ai-fab" aria-label="Open IDMatch AI Support"><span>♡</span><b>Support</b></button>
      <section class="idmatch-ai-panel" aria-live="polite" aria-label="IDMatch AI Support">
        <div class="idmatch-ai-head"><div><strong>IDMatch AI Support</strong><small>Online • Here to help 24/7</small></div><button class="idmatch-ai-close" aria-label="Close">×</button></div>
        <div class="idmatch-ai-messages"><div class="idmatch-msg bot">Hi! I’m the IDMatch AI Support Assistant. I can help with your account, profile, matches, verification, membership, privacy, and more.</div></div>
        <div class="idmatch-ai-quick"><button>How do I edit my profile?</button><button>I need help with my matches</button><button>How do I contact human support?</button></div>
        <form class="idmatch-ai-form"><input maxlength="2000" placeholder="Type your question..." autocomplete="off"><button>➤</button></form>
        <a class="idmatch-ai-wa" href="https://wa.me/923XXXXXXXXX" target="_blank" rel="noopener">Continue on WhatsApp</a>
      </section>`;
    document.body.appendChild(root);
    const fab=root.querySelector('.idmatch-ai-fab'),panel=root.querySelector('.idmatch-ai-panel'),close=root.querySelector('.idmatch-ai-close'),form=root.querySelector('form'),input=form.querySelector('input'),messages=root.querySelector('.idmatch-ai-messages');
    const open=()=>{panel.classList.add('open');input.focus()}; fab.onclick=open;close.onclick=()=>panel.classList.remove('open');
    function add(text,who){const div=document.createElement('div');div.className='idmatch-msg '+who;div.textContent=text;messages.appendChild(div);messages.scrollTop=messages.scrollHeight;return div}
    async function ask(text){if(!text.trim())return;add(text,'user');input.value='';const typing=add('Typing…','bot typing');try{const r=await fetch('/support/api.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({message:text})});const d=await r.json();typing.remove();add(d.reply||'I’m unable to respond right now. Please use human support.','bot')}catch(e){typing.remove();add('I’m having trouble connecting right now. Please use human support or WhatsApp.','bot')}}
    form.addEventListener('submit',e=>{e.preventDefault();ask(input.value)});root.querySelectorAll('.idmatch-ai-quick button').forEach(b=>b.addEventListener('click',()=>{open();ask(b.textContent)}));
  }
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',mount);else mount();
})();
