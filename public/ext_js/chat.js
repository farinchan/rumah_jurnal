/**
 * Live Chat Embed Widget
 * Rumah Jurnal UIN Bukittinggi
 * Modern floating chat widget dengan API integration (tanpa WhatsApp)
 */

(function() {
    'use strict';

    // Configuration
    const ChatConfig = {
        // API Configuration
        apiBaseUrl: 'https://rumahjurnal.uinbukittinggi.ac.id/api',
        apiEndpoints: {
            sendMessage: '/bot',
            getStatus: '/chat/status'
        },

        // Widget Configuration
        title: 'Rumah Jurnal Support',
        subtitle: 'Kami siap membantu Anda',
        welcomeMessage: 'Halo! 👋 Ada yang bisa kami bantu? Silakan pilih topik atau ketik pertanyaan Anda.',
        operatingHours: '08:00 - 16:00 WIB (Senin - Jumat)',
        position: 'right', // 'left' or 'right'
        primaryColor: '#0f4aa2',
        secondaryColor: '#0fa36b',

        // Auto Response (fallback jika API tidak merespons)
        autoResponses: {
            default: 'Terima kasih atas pesan Anda. Tim kami akan segera merespons.',
            offline: 'Maaf, saat ini kami sedang offline. Pesan Anda telah kami terima dan akan dibalas pada jam kerja.',
            error: 'Maaf, terjadi kesalahan. Silakan coba lagi nanti.'
        },

        // Quick Replies
        quickReplies: [
            { text: '📚 Cara Submit Artikel', message: 'Bagaimana cara submit artikel ke jurnal?' },
            { text: '📋 Status pembayaran', message: 'cek status pembayaran artikel saya, ini adalah nomor artikel saya: ' },
            { text: '💰 Biaya Publikasi', message: 'Berapa biaya publikasi jurnal?' },
            { text: '🎓 Akreditasi Jurnal', message: 'Apa saja jurnal yang sudah terakreditasi?' },
            // { text: '📞 Kontak Admin', message: 'Saya ingin berbicara dengan admin.' }
        ]
    };

    // Chat Widget Class
    class LiveChatWidget {
        constructor(config) {
            this.config = config;
            this.isOpen = false;
            this.isTyping = false;
            this.sessionId = this.getOrCreateSessionId();
            this.visitorInfo = this.getVisitorInfo();
            this.messages = [];
            this.init();
        }

        // Generate atau ambil session ID dari localStorage
        getOrCreateSessionId() {
            let sessionId = localStorage.getItem('chat_session_id');
            if (!sessionId) {
                sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                localStorage.setItem('chat_session_id', sessionId);
            }
            return sessionId;
        }

        // Ambil informasi visitor
        getVisitorInfo() {
            return {
                userAgent: navigator.userAgent,
                language: navigator.language,
                platform: navigator.platform,
                screenWidth: window.screen.width,
                screenHeight: window.screen.height,
                referrer: document.referrer,
                currentUrl: window.location.href,
                timestamp: new Date().toISOString()
            };
        }

        init() {
            this.injectStyles();
            this.createWidget();
            this.bindEvents();
            this.loadChatHistory();
        }

        injectStyles() {
            const styles = `
                /* ==========================================
                   CHAT WIDGET STYLES - Scoped dengan prefix
                   ========================================== */

                /* Base Container */
                .chat-widget-container {
                    all: revert !important;
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
                    font-size: 16px !important;
                    line-height: 1.5 !important;
                    color: #333 !important;
                    position: fixed !important;
                    bottom: 20px !important;
                    ${this.config.position}: 20px !important;
                    z-index: 2147483647 !important;
                    box-sizing: border-box !important;
                }

                .chat-widget-container * {
                    box-sizing: border-box !important;
                }

                /* Chat Toggle Button */
                .chat-widget-container .chat-toggle-btn {
                    all: revert !important;
                    width: 60px !important;
                    height: 60px !important;
                    border-radius: 50% !important;
                    background: linear-gradient(135deg, ${this.config.primaryColor}, ${this.config.secondaryColor}) !important;
                    border: none !important;
                    cursor: pointer !important;
                    box-shadow: 0 4px 20px rgba(15, 74, 162, 0.4) !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    transition: all 0.3s ease !important;
                    position: relative !important;
                    padding: 0 !important;
                    margin: 0 !important;
                    outline: none !important;
                }

                .chat-widget-container .chat-toggle-btn:hover {
                    transform: scale(1.1) !important;
                    box-shadow: 0 6px 30px rgba(15, 74, 162, 0.5) !important;
                }

                /* Icon dalam Toggle Button */
                .chat-widget-container .chat-toggle-btn .btn-icon {
                    width: 28px !important;
                    height: 28px !important;
                    transition: all 0.3s ease !important;
                }

                .chat-widget-container .chat-toggle-btn .btn-icon.chat-icon {
                    display: block !important;
                }

                .chat-widget-container .chat-toggle-btn .btn-icon.close-icon {
                    display: none !important;
                    position: absolute !important;
                }

                .chat-widget-container .chat-toggle-btn.active .btn-icon.chat-icon {
                    display: none !important;
                }

                .chat-widget-container .chat-toggle-btn.active .btn-icon.close-icon {
                    display: block !important;
                }

                /* Notification Badge */
                .chat-widget-container .chat-notification {
                    all: revert !important;
                    position: absolute !important;
                    top: -5px !important;
                    right: -5px !important;
                    width: 20px !important;
                    height: 20px !important;
                    background: #ff4757 !important;
                    border-radius: 50% !important;
                    color: white !important;
                    font-size: 12px !important;
                    font-weight: 600 !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    font-family: inherit !important;
                    line-height: 1 !important;
                    padding: 0 !important;
                    margin: 0 !important;
                    animation: chatPulse 2s infinite !important;
                }

                @keyframes chatPulse {
                    0%, 100% { transform: scale(1); }
                    50% { transform: scale(1.1); }
                }

                /* Chat Window */
                .chat-widget-container .chat-window {
                    all: revert !important;
                    position: absolute !important;
                    bottom: 80px !important;
                    ${this.config.position}: 0 !important;
                    width: 380px !important;
                    max-width: calc(100vw - 40px) !important;
                    height: 520px !important;
                    max-height: calc(100vh - 150px) !important;
                    background: white !important;
                    border-radius: 16px !important;
                    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
                    display: none !important;
                    flex-direction: column !important;
                    overflow: hidden !important;
                    font-family: inherit !important;
                }

                .chat-widget-container .chat-window.open {
                    display: flex !important;
                }

                /* Chat Header */
                .chat-widget-container .chat-header {
                    all: revert !important;
                    background: linear-gradient(135deg, ${this.config.primaryColor}, ${this.config.secondaryColor}) !important;
                    padding: 16px 20px !important;
                    color: white !important;
                    position: relative !important;
                    display: flex !important;
                    align-items: center !important;
                    gap: 12px !important;
                    flex-shrink: 0 !important;
                }

                .chat-widget-container .chat-avatar {
                    all: revert !important;
                    width: 45px !important;
                    height: 45px !important;
                    border-radius: 50% !important;
                    background: rgba(255, 255, 255, 0.2) !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    flex-shrink: 0 !important;
                }

                .chat-widget-container .chat-avatar .avatar-icon {
                    width: 24px !important;
                    height: 24px !important;
                }

                .chat-widget-container .chat-header-info {
                    all: revert !important;
                    flex: 1 !important;
                    min-width: 0 !important;
                }

                .chat-widget-container .chat-header-title {
                    all: revert !important;
                    font-size: 16px !important;
                    font-weight: 600 !important;
                    margin: 0 0 4px 0 !important;
                    color: white !important;
                    font-family: inherit !important;
                }

                .chat-widget-container .chat-header-status {
                    all: revert !important;
                    font-size: 13px !important;
                    opacity: 0.9 !important;
                    display: flex !important;
                    align-items: center !important;
                    gap: 6px !important;
                    margin: 0 !important;
                    color: white !important;
                    font-family: inherit !important;
                }

                .chat-widget-container .status-dot {
                    all: revert !important;
                    width: 8px !important;
                    height: 8px !important;
                    background: #4ade80 !important;
                    border-radius: 50% !important;
                    display: inline-block !important;
                }

                .chat-widget-container .chat-close-btn {
                    all: revert !important;
                    position: absolute !important;
                    top: 12px !important;
                    right: 12px !important;
                    width: 28px !important;
                    height: 28px !important;
                    border: none !important;
                    background: rgba(255, 255, 255, 0.2) !important;
                    border-radius: 50% !important;
                    cursor: pointer !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    padding: 0 !important;
                    transition: background 0.2s !important;
                }

                .chat-widget-container .chat-close-btn:hover {
                    background: rgba(255, 255, 255, 0.3) !important;
                }

                .chat-widget-container .chat-close-btn .close-icon {
                    width: 14px !important;
                    height: 14px !important;
                }

                /* Chat Body */
                .chat-widget-container .chat-body {
                    all: revert !important;
                    flex: 1 !important;
                    padding: 16px !important;
                    overflow-y: auto !important;
                    background: #f5f7fb !important;
                    display: flex !important;
                    flex-direction: column !important;
                }

                .chat-widget-container .chat-messages {
                    all: revert !important;
                    flex: 1 !important;
                    display: flex !important;
                    flex-direction: column !important;
                    gap: 10px !important;
                }

                /* Message Bubble */
                .chat-widget-container .chat-message {
                    all: revert !important;
                    max-width: 80% !important;
                    padding: 10px 14px !important;
                    border-radius: 16px !important;
                    font-size: 14px !important;
                    line-height: 1.4 !important;
                    word-wrap: break-word !important;
                    font-family: inherit !important;
                }

                .chat-widget-container .chat-message.user {
                    background: linear-gradient(135deg, ${this.config.primaryColor}, ${this.config.secondaryColor}) !important;
                    color: white !important;
                    margin-left: auto !important;
                    border-bottom-right-radius: 4px !important;
                }

                .chat-widget-container .chat-message.admin {
                    background: white !important;
                    color: #333 !important;
                    margin-right: auto !important;
                    border-bottom-left-radius: 4px !important;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
                }

                .chat-widget-container .message-time {
                    all: revert !important;
                    font-size: 11px !important;
                    opacity: 0.7 !important;
                    margin-top: 4px !important;
                    display: block !important;
                    font-family: inherit !important;
                }

                /* Typing Indicator */
                .chat-widget-container .typing-indicator {
                    all: revert !important;
                    display: flex !important;
                    align-items: center !important;
                    gap: 4px !important;
                    padding: 10px 14px !important;
                    background: white !important;
                    border-radius: 16px !important;
                    border-bottom-left-radius: 4px !important;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
                    width: fit-content !important;
                }

                .chat-widget-container .typing-indicator span {
                    all: revert !important;
                    width: 8px !important;
                    height: 8px !important;
                    background: #aaa !important;
                    border-radius: 50% !important;
                    display: inline-block !important;
                    animation: typingBounce 1.4s infinite !important;
                }

                .chat-widget-container .typing-indicator span:nth-child(2) { animation-delay: 0.2s !important; }
                .chat-widget-container .typing-indicator span:nth-child(3) { animation-delay: 0.4s !important; }

                @keyframes typingBounce {
                    0%, 60%, 100% { transform: translateY(0); }
                    30% { transform: translateY(-4px); }
                }

                /* Quick Replies */
                .chat-widget-container .quick-replies {
                    all: revert !important;
                    display: flex !important;
                    flex-wrap: wrap !important;
                    gap: 8px !important;
                    margin-top: 12px !important;
                    padding-top: 12px !important;
                }

                .chat-widget-container .quick-reply-btn {
                    all: revert !important;
                    background: white !important;
                    border: 1px solid #e0e0e0 !important;
                    padding: 8px 12px !important;
                    border-radius: 16px !important;
                    font-size: 13px !important;
                    color: #333 !important;
                    cursor: pointer !important;
                    transition: all 0.2s !important;
                    font-family: inherit !important;
                }

                .chat-widget-container .quick-reply-btn:hover {
                    background: ${this.config.primaryColor} !important;
                    border-color: ${this.config.primaryColor} !important;
                    color: white !important;
                }

                /* Chat Footer */
                .chat-widget-container .chat-footer {
                    all: revert !important;
                    padding: 12px 16px !important;
                    background: white !important;
                    border-top: 1px solid #eee !important;
                    flex-shrink: 0 !important;
                }

                .chat-widget-container .chat-input-group {
                    all: revert !important;
                    display: flex !important;
                    gap: 8px !important;
                    align-items: center !important;
                }

                .chat-widget-container .chat-input {
                    all: revert !important;
                    flex: 1 !important;
                    padding: 10px 16px !important;
                    border: 1px solid #e0e0e0 !important;
                    border-radius: 24px !important;
                    font-size: 14px !important;
                    outline: none !important;
                    font-family: inherit !important;
                    background: #f5f7fb !important;
                }

                .chat-widget-container .chat-input:focus {
                    border-color: ${this.config.primaryColor} !important;
                    background: white !important;
                }

                .chat-widget-container .chat-send-btn {
                    all: revert !important;
                    width: 44px !important;
                    height: 44px !important;
                    border-radius: 50% !important;
                    background: linear-gradient(135deg, ${this.config.primaryColor}, ${this.config.secondaryColor}) !important;
                    border: none !important;
                    cursor: pointer !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    transition: transform 0.2s !important;
                    flex-shrink: 0 !important;
                    padding: 0 !important;
                }

                .chat-widget-container .chat-send-btn:hover {
                    transform: scale(1.05) !important;
                }

                .chat-widget-container .chat-send-btn:disabled {
                    opacity: 0.5 !important;
                    cursor: not-allowed !important;
                }

                .chat-widget-container .chat-send-btn .send-icon {
                    width: 20px !important;
                    height: 20px !important;
                }

                /* Mobile */
                @media (max-width: 480px) {
                    .chat-widget-container .chat-window {
                        width: calc(100vw - 30px) !important;
                        height: calc(100vh - 100px) !important;
                        bottom: 70px !important;
                    }

                    .chat-widget-container .chat-toggle-btn {
                        width: 54px !important;
                        height: 54px !important;
                    }
                }
            `;

            const styleSheet = document.createElement('style');
            styleSheet.textContent = styles;
            document.head.appendChild(styleSheet);
        }

        createWidget() {
            // Base64 encoded SVG icons
            const icons = {
                chat: 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48cGF0aCBmaWxsPSJ3aGl0ZSIgZD0iTTUxMiAyNDBjMCAxMTQuOS0xMTQuNiAyMDgtMjU2IDIwOGMtMzcuMSAwLTcyLjMtNi40LTEwNC4xLTE3LjljLTExLjkgOC43LTMxLjMgMjAuNi01NC4zIDMwLjZDNzMuNiA0NzEuMSA0NC43IDQ4MCAxNiA0ODBjLTYuNSAwLTEyLjMtMy45LTE0LjgtOS45Yy0yLjUtNi0xLjEtMTIuOCAzLjQtMTcuNGwwIDAgMCAwIDAgMCAwIDAgLjMtLjNjLjMtLjMgLjctLjcgMS4zLTEuNGMxLjEtMS4yIDIuOC0zLjEgNC45LTUuN2M0LjEtNSA5LjYtMTIuNCAxNS4yLTIxLjZjMTAtMTYuNiAxOS41LTM4LjQgMjEuNC02Mi45QzE3LjcgMzI2LjggMCAyODUuMSAwIDI0MEMwIDEyNS4xIDExNC42IDMyIDI1NiAzMnMyNTYgOTMuMSAyNTYgMjA4eiIvPjwvc3ZnPg==',
                close: 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzODQgNTEyIj48cGF0aCBmaWxsPSJ3aGl0ZSIgZD0iTTM0Mi42IDE1MC42YzEyLjUtMTIuNSAxMi41LTMyLjggMC00NS4zcy0zMi44LTEyLjUtNDUuMyAwTDE5MiAyMTAuNyA4Ni42IDEwNS40Yy0xMi41LTEyLjUtMzIuOC0xMi41LTQ1LjMgMHMtMTIuNSAzMi44IDAgNDUuM0wxNDYuNyAyNTYgNDEuNCAzNjEuNGMtMTIuNSAxMi41LTEyLjUgMzIuOCAwIDQ1LjNzMzIuOCAxMi41IDQ1LjMgMEwxOTIgMzAxLjMgMjk3LjQgNDA2LjZjMTIuNSAxMi41IDMyLjggMTIuNSA0NS4zIDBzMTIuNS0zMi44IDAtNDUuM0wyMzcuMyAyNTYgMzQyLjYgMTUwLjZ6Ii8+PC9zdmc+',
                headset: 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48cGF0aCBmaWxsPSJ3aGl0ZSIgZD0iTTI1NiA0OEMxNDEuMSA0OCA0OCAxNDEuMSA0OCAyNTZ2NDBjMCAxMy4zLTEwLjcgMjQtMjQgMjRzLTI0LTEwLjctMjQtMjRWMjU2QzAgMTE0LjYgMTE0LjYgMCAyNTYgMFM1MTIgMTE0LjYgNTEyIDI1NlY0MDAuMWMwIDQ4LjYtMzkuNCA4OC04OC4xIDg4TDMxMy42IDQ4OGMtOC4zIDE0LjMtMjMuOCAyNC00MS42IDI0SDI0MGMtMjYuNSAwLTQ4LTIxLjUtNDgtNDhzMjEuNS00OCA0OC00OGgzMmMxNy44IDAgMzMuMyA5LjcgNDEuNiAyNGwxMTAuNCAuMWMyMi4xIDAgNDAtMTcuOSA0MC00MFYyNTZjMC0xMTQuOS05My4xLTIwOC0yMDgtMjA4ek0xNDQgMjA4aDE2YzE3LjcgMCAzMiAxNC4zIDMyIDMyVjM1MmMwIDE3LjctMTQuMyAzMi0zMiAzMkgxNDRjLTM1LjMgMC02NC0yOC43LTY0LTY0VjI3MmMwLTM1LjMgMjguNy02NCA2NC02NHptMjI0IDBjMzUuMyAwIDY0IDI4LjcgNjQgNjR2NDhjMCAzNS4zLTI4LjcgNjQtNjQgNjRIMzUyYy0xNy43IDAtMzItMTQuMy0zMi0zMlYyNDBjMC0xNy43IDE0LjMtMzIgMzItMzJoMTZ6Ii8+PC9zdmc+',
                send: 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48cGF0aCBmaWxsPSJ3aGl0ZSIgZD0iTTQ5OC4xIDUuNmMxMC4xIDcgMTUuNCAxOS4xIDEzLjUgMzEuMmwtNjQgNDE2Yy0xLjUgOS43LTcuNCAxOC4yLTE2IDIzcy0xOC45IDUuNC0yOCAxLjZMMjg0IDQyNy43bC02OC41IDc0LjFjLTguOSA5LjctMjIuOSAxMi45LTM1LjIgOC4xUzE2MCA0OTIuNCAxNjAgNDgwVjM5Ni40YzAtNCAxLjUtNy44IDQuMi0xMC43TDMzMS44IDIwMi44YzUuOC02LjMgNS42LTE2LS40LTIycy0xNS43LTYuNC0yMi0uN0wxMDYgMzYwLjggMTcuNyAzMTYuNkM3LjEgMzExLjMgLjMgMzAwLjcgMCAyODkuMXM1LjktMjIuOCAxNi4xLTI4LjdsNDQ4LTI1NmMxMC43LTYuMSAyMy45LTUuNSAzNCAxLjR6Ii8+PC9zdmc+'
            };

            const container = document.createElement('div');
            container.className = 'chat-widget-container';
            container.innerHTML = `
                <!-- Chat Toggle Button -->
                <button class="chat-toggle-btn" id="chatToggleBtn" aria-label="Open chat">
                    <img class="btn-icon chat-icon" src="${icons.chat}" alt="Chat">
                    <img class="btn-icon close-icon" src="${icons.close}" alt="Close">
                    <span class="chat-notification">1</span>
                </button>

                <!-- Chat Window -->
                <div class="chat-window" id="chatWindow">
                    <!-- Header -->
                    <div class="chat-header">
                        <button class="chat-close-btn" id="chatCloseBtn" aria-label="Close chat">
                            <img class="close-icon" src="${icons.close}" alt="Close">
                        </button>
                        <div class="chat-avatar">
                            <img class="avatar-icon" src="${icons.headset}" alt="Support">
                        </div>
                        <div class="chat-header-info">
                            <h3 class="chat-header-title">${this.config.title}</h3>
                            <p class="chat-header-status">
                                <span class="status-dot"></span>
                                <span id="statusText">${this.config.subtitle}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="chat-body" id="chatBody">
                        <!-- Messages Container -->
                        <div class="chat-messages" id="chatMessages"></div>

                        <!-- Quick Replies -->
                        <div class="quick-replies" id="quickReplies">
                            ${this.config.quickReplies.map(reply => `
                                <button class="quick-reply-btn" data-message="${reply.message}">
                                    ${reply.text}
                                </button>
                            `).join('')}
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="chat-footer">
                        <div class="chat-input-group">
                            <input type="text" class="chat-input" id="chatInput" placeholder="Ketik pesan Anda..." maxlength="500">
                            <button class="chat-send-btn" id="chatSendBtn" aria-label="Send message">
                                <img class="send-icon" src="${icons.send}" alt="Send">
                            </button>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(container);

            // Store references
            this.container = container;
            this.toggleBtn = container.querySelector('#chatToggleBtn');
            this.chatWindow = container.querySelector('#chatWindow');
            this.closeBtn = container.querySelector('#chatCloseBtn');
            this.chatBody = container.querySelector('#chatBody');
            this.messagesContainer = container.querySelector('#chatMessages');
            this.quickRepliesContainer = container.querySelector('#quickReplies');
            this.input = container.querySelector('#chatInput');
            this.sendBtn = container.querySelector('#chatSendBtn');
            this.notification = container.querySelector('.chat-notification');
            this.statusText = container.querySelector('#statusText');
        }

        bindEvents() {
            // Toggle chat window
            this.toggleBtn.addEventListener('click', () => this.toggleChat());
            this.closeBtn.addEventListener('click', () => this.closeChat());

            // Send message
            this.sendBtn.addEventListener('click', () => this.sendMessage());
            this.input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });

            // Quick reply buttons
            this.quickRepliesContainer.addEventListener('click', (e) => {
                const btn = e.target.closest('.quick-reply-btn');
                if (btn) {
                    const message = btn.dataset.message;
                    this.input.value = message;
                    this.sendMessage();
                }
            });

            // Close on outside click
            document.addEventListener('click', (e) => {
                if (this.isOpen && !this.container.contains(e.target)) {
                    this.closeChat();
                }
            });

            // Close on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isOpen) {
                    this.closeChat();
                }
            });
        }

        toggleChat() {
            if (this.isOpen) {
                this.closeChat();
            } else {
                this.openChat();
            }
        }

        openChat() {
            this.isOpen = true;
            this.toggleBtn.classList.add('active');
            this.chatWindow.classList.add('open');
            this.notification.style.display = 'none';
            this.input.focus();

            // Add welcome message if no messages yet
            if (this.messages.length === 0) {
                this.addMessage(this.config.welcomeMessage, 'admin');
            }


            // Scroll to bottom
            this.scrollToBottom();
        }

        closeChat() {
            this.isOpen = false;
            this.toggleBtn.classList.remove('active');
            this.chatWindow.classList.remove('open');

        }

        async sendMessage() {
            const message = this.input.value.trim();
            if (!message || this.isTyping) return;

            // Clear input
            this.input.value = '';

            // Add user message to UI
            this.addMessage(message, 'user');

            // Hide quick replies after first message
            this.quickRepliesContainer.style.display = 'none';

            // Show typing indicator
            this.showTypingIndicator();

            // Send to API
            try {
                const response = await this.sendMessageToAPI(message);

                // Hide typing indicator
                this.hideTypingIndicator();

                // Show response from API
                if (response && response.reply) {
                    this.addMessage(response.reply, 'admin');
                } else if (response && response.message) {
                    this.addMessage(response.message, 'admin');
                } else {
                    // Fallback response
                    this.addMessage(this.config.autoResponses.default, 'admin');
                }
            } catch (error) {
                console.error('Error sending message:', error);
                this.hideTypingIndicator();
                this.addMessage(this.config.autoResponses.default, 'admin');
            }
        }

        // Format text with WhatsApp-style formatting
        formatText(text) {
            let formatted = text;

            // Escape HTML first to prevent XSS
            formatted = formatted
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');

            // Bold: *text*
            formatted = formatted.replace(/\*(.*?)\*/g, '<strong>$1</strong>');

            // Italic: _text_
            formatted = formatted.replace(/_(.*?)_/g, '<em>$1</em>');

            // Strikethrough: ~text~
            formatted = formatted.replace(/~(.*?)~/g, '<del>$1</del>');

            // Monospace: `text`
            formatted = formatted.replace(/`(.*?)`/g, '<code style="background: rgba(0,0,0,0.05); padding: 2px 4px; border-radius: 3px; font-family: monospace;">$1</code>');

            // Convert newlines to <br>
            formatted = formatted.replace(/\n/g, '<br>');

            return formatted;
        }

        addMessage(text, sender = 'user') {
            const messageData = {
                id: Date.now(),
                text: text,
                sender: sender,
                timestamp: new Date().toISOString()
            };

            // Save to messages array
            this.messages.push(messageData);
            this.saveChatToLocal();

            // Create message element
            const messageEl = document.createElement('div');
            messageEl.className = `chat-message ${sender}`;
            // Format text with WhatsApp-style formatting
            const formattedText = this.formatText(text);
            messageEl.innerHTML = `
                ${formattedText}
                <span class="message-time">${this.formatTime(messageData.timestamp)}</span>
            `;

            // Add to container
            this.messagesContainer.appendChild(messageEl);

            // Scroll to bottom
            this.scrollToBottom();
        }

        showTypingIndicator() {
            this.isTyping = true;
            this.sendBtn.disabled = true;

            const typingEl = document.createElement('div');
            typingEl.className = 'typing-indicator';
            typingEl.id = 'typingIndicator';
            typingEl.innerHTML = '<span></span><span></span><span></span>';

            this.messagesContainer.appendChild(typingEl);
            this.scrollToBottom();
        }

        hideTypingIndicator() {
            this.isTyping = false;
            this.sendBtn.disabled = false;

            const typingEl = document.getElementById('typingIndicator');
            if (typingEl) {
                typingEl.remove();
            }
        }

        scrollToBottom() {
            setTimeout(() => {
                this.chatBody.scrollTop = this.chatBody.scrollHeight;
            }, 100);
        }

        formatTime(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // ==========================================
        // API METHODS
        // ==========================================

        async sendMessageToAPI(message) {
            const payload = {
                session_id: this.sessionId,
                message: message,
                visitor: this.visitorInfo,
                timestamp: new Date().toISOString(),
                metadata: {
                    page_title: document.title,
                    page_url: window.location.href
                }
            };

            const response = await fetch(`${this.config.apiBaseUrl}${this.config.apiEndpoints.sendMessage}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        }


        loadChatHistory() {
            // Load chat history dari localStorage saja
            this.loadChatFromLocal();
        }

        saveChatToLocal() {
            try {
                localStorage.setItem('chat_messages_' + this.sessionId, JSON.stringify(this.messages));
            } catch (error) {
                console.error('Error saving to localStorage:', error);
            }
        }

        loadChatFromLocal() {
            try {
                const stored = localStorage.getItem('chat_messages_' + this.sessionId);
                if (stored) {
                    this.messages = JSON.parse(stored);
                    this.renderChatHistory();
                }
            } catch (error) {
                console.error('Error loading from localStorage:', error);
            }
        }

        renderChatHistory() {
            this.messagesContainer.innerHTML = '';

            this.messages.forEach(msg => {
                const messageEl = document.createElement('div');
                messageEl.className = `chat-message ${msg.sender}`;
                // Format text with WhatsApp-style formatting
                const formattedText = this.formatText(msg.text);
                messageEl.innerHTML = `
                    ${formattedText}
                    <span class="message-time">${this.formatTime(msg.timestamp)}</span>
                `;
                this.messagesContainer.appendChild(messageEl);
            });

            // Hide quick replies if there are messages
            if (this.messages.length > 1) {
                this.quickRepliesContainer.style.display = 'none';
            }

            this.scrollToBottom();
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            new LiveChatWidget(ChatConfig);
        });
    } else {
        new LiveChatWidget(ChatConfig);
    }
})();
