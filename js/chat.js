function refreshMessages() {
    const chatWithId = document.querySelector('[name="receiver_id"]').value;
  
    fetch(`../actions/get_messages.php?user=${chatWithId}`)
      .then(response => response.text())
      .then(html => {
        const messagesDiv = document.getElementById('messages');
        messagesDiv.innerHTML = html;
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
      });
  }

setInterval(refreshMessages, 5000);

window.addEventListener('focus', refreshMessages);


function autoGrow(element) {
    element.style.height = 'auto';
    element.style.height = (element.scrollHeight) + 'px';
}

document.querySelector('textarea').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        this.form.requestSubmit();
    }
});
function scrollToBottom() {
    const container = document.querySelector('.chat-messages');
    if (container) {
    container.scrollTop = container.scrollHeight;
    }
}

window.addEventListener('load', scrollToBottom);

const observer = new MutationObserver(scrollToBottom);
const chatMessages = document.querySelector('.chat-messages');
if (chatMessages) {
    observer.observe(chatMessages, { childList: true });
}
window.addEventListener('load', () => {
    const textarea = document.querySelector('textarea[name="content"]');
    if (textarea) textarea.focus();
});
document.addEventListener('keydown', (e) => {
    const active = document.activeElement;
    const textarea = document.querySelector('textarea[name="content"]');

    if (
        textarea &&
        !['INPUT', 'TEXTAREA', 'BUTTON'].includes(active.tagName) &&
        e.key.length === 1 
    ) {
        textarea.focus();
    }
 });