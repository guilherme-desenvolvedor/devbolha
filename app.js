/**
 * DevBolha - interações do navegador.
 * As funções abaixo controlam apenas a interface deste protótipo.
 * Dados e ações ainda não são sincronizados com um banco de dados.
 */

// 1. Feedback visual e compartilhamento do convite.
function toast(message) {
    const toastElement = document.getElementById('toast');
    if (!toastElement) return;

    toastElement.textContent = message;
    toastElement.classList.add('show');
    clearTimeout(window.toastTimer);
    window.toastTimer = setTimeout(() => toastElement.classList.remove('show'), 2400);
}

function copyInvite() {
    const inviteUrl = `${location.origin}${location.pathname}?invite=DEVBOLHA`;

    if (!navigator.clipboard) {
        toast('Convite: DEVBOLHA');
        return;
    }

    navigator.clipboard.writeText(inviteUrl)
        .then(() => toast('Link de convite copiado!'))
        .catch(() => toast('Convite: DEVBOLHA'));
}

// 2. Ações visuais das publicações: curtir, comentar e compartilhar.
function likePost(button) {
    button.classList.toggle('liked');

    const post = button.closest('.post');
    const likeCount = post.querySelector('.like-count');
    const isLiked = button.classList.contains('liked');
    const change = isLiked ? 1 : -1;

    likeCount.textContent = Number(likeCount.textContent) + change;
    button.querySelector('span').textContent = isLiked ? 'Curtido' : 'Curtir';
}

function toggleComments(button) {
    const commentBox = button.closest('.post').querySelector('.comment-box');
    commentBox.classList.toggle('show');

    if (commentBox.classList.contains('show')) {
        commentBox.querySelector('input').focus();
    }
}

function sharePost() {
    if (!navigator.clipboard) {
        toast('Copie o endereço do navegador para compartilhar.');
        return;
    }

    navigator.clipboard.writeText(location.href)
        .then(() => toast('Link copiado para compartilhar!'));
}

// 3. Solicitações de conexão demonstrativas.
function connect(button) {
    if (button.dataset.connected) {
        button.textContent = '+';
        button.removeAttribute('data-connected');
        toast('Solicitação cancelada.');
        return;
    }

    button.textContent = '✓';
    button.dataset.connected = '1';
    toast('Solicitação de conexão enviada!');
}

// 4. Pesquisa e filtros de conteúdo.
function searchContent(query) {
    const searchTerm = query.toLowerCase();

    document.querySelectorAll('[data-search]').forEach((element) => {
        element.style.display = element.dataset.search.includes(searchTerm) ? '' : 'none';
    });
}

function sortPosts(order) {
    const postsContainer = document.getElementById('posts');
    if (!postsContainer) return;

    const posts = [...postsContainer.children];
    posts.sort((first, second) => {
        if (order === 'popular') {
            return Number(second.dataset.likes) - Number(first.dataset.likes);
        }

        return Number(second.dataset.order || 0) - Number(first.dataset.order || 0);
    });

    posts.forEach((post) => postsContainer.appendChild(post));
}

function filterProjects(status, selectedButton) {
    document.querySelectorAll('.filter').forEach((button) => button.classList.remove('active'));
    selectedButton.classList.add('active');

    document.querySelectorAll('.project-card').forEach((card) => {
        const shouldShow = status === 'all' || card.dataset.status === status;
        card.style.display = shouldShow ? '' : 'none';
    });
}

function filterConversations(query) {
    const searchTerm = query.toLowerCase();

    document.querySelectorAll('.conversation').forEach((conversation) => {
        conversation.style.display = conversation.dataset.name.includes(searchTerm) ? 'flex' : 'none';
    });
}

// 5. Troca de conversa e envio de mensagens fictícias na interface.
function selectConversation(button, name, imageFile) {
    document.querySelectorAll('.conversation').forEach((conversation) => {
        conversation.classList.remove('selected');
    });

    button.classList.add('selected');
    document.getElementById('chatName').textContent = name;
    document.getElementById('chatAvatar').src = `assets/images/${imageFile}`;
    document.getElementById('chatContent').innerHTML = `
        <div class="day-divider">CONVERSA</div>
        <p class="bubble received">Olá! Que bom falar com você. Vamos trocar ideias sobre projetos?</p>
    `;
}

function sendMessage(event) {
    event.preventDefault();

    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    if (!message) return;

    const messageBubble = document.createElement('p');
    messageBubble.className = 'bubble sent';
    messageBubble.textContent = message;

    const chatContent = document.getElementById('chatContent');
    chatContent.appendChild(messageBubble);
    messageInput.value = '';
    chatContent.scrollTop = chatContent.scrollHeight;
}

// 6. Atalhos globais: Ctrl/Cmd + K foca a pesquisa; Esc fecha modais.
document.addEventListener('keydown', (event) => {
    if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
        event.preventDefault();
        document.getElementById('globalSearch')?.focus();
    }

    if (event.key === 'Escape') {
        document.querySelectorAll('.modal.show').forEach((modal) => {
            modal.classList.remove('show');
        });
    }
});
