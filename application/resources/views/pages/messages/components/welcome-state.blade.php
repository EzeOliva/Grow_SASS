<!-- Beautiful Welcome State - Modern Messages Page -->
<div class="welcome-state">
    <div class="welcome-container">
        <!-- Main Welcome Content -->
        <div class="welcome-main">
            <div class="welcome-icon">
                <div class="icon-circle">
                    <i class="fas fa-comments"></i>
                </div>
            </div>
            <h1 class="welcome-title">Welcome to Messages</h1>
            <p class="welcome-description">
                Start connecting with your team and customers through beautiful, seamless messaging. 
                Choose from internal team chat or WhatsApp business integration.
            </p>
        </div>

        <!-- Feature Cards -->
        <div class="features-grid">
            <div class="feature-card team-chat">
                <div class="feature-icon">
                    <i class="fas fa-user-friends"></i>
                </div>
                <div class="feature-content">
                    <h3>Team Chat</h3>
                    <p>Connect with your team members instantly</p>
                </div>
                <div class="feature-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </div>

            <div class="feature-card whatsapp">
                <div class="feature-icon">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <div class="feature-content">
                    <h3>WhatsApp Business</h3>
                    <p>Manage customer conversations professionally</p>
                </div>
                <div class="feature-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </div>

            <div class="feature-card history">
                <div class="feature-icon">
                    <i class="fas fa-history"></i>
                </div>
                <div class="feature-content">
                    <h3>Message History</h3>
                    <p>Access all your conversations anytime</p>
                </div>
                <div class="feature-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <button class="action-btn primary">
                <i class="fas fa-plus"></i>
                <span>Conversation</span>
            </button>
            <button class="action-btn secondary">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </button>
        </div>
    </div>
</div>

<!-- Beautiful CSS for Welcome State -->
<style>
/* ===========================================
   BEAUTIFUL WELCOME STATE DESIGN
   =========================================== */

.welcome-state {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    padding: 48px 24px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    min-height: 600px;
}

.welcome-container {
    max-width: 800px;
    width: 100%;
    text-align: center;
}

/* Main Welcome Content */
.welcome-main {
    margin-bottom: 64px;
}

.welcome-icon {
    margin-bottom: 32px;
}

.icon-circle {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    box-shadow: var(--shadow-heavy);
    position: relative;
    overflow: hidden;
}

.icon-circle::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    animation: shimmer 3s infinite;
}

.icon-circle i {
    font-size: 48px;
    color: var(--white);
    z-index: 2;
    position: relative;
}

@keyframes shimmer {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

.welcome-title {
    font-size: 48px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 24px 0;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1.2;
}

.welcome-description {
    font-size: 20px;
    color: var(--text-secondary);
    line-height: 1.6;
    margin: 0;
    max-width: 600px;
    margin: 0 auto;
}

/* Features Grid */
.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 48px;
}

.feature-card {
    background: var(--white);
    border-radius: var(--radius-lg);
    padding: 32px 24px;
    text-align: left;
    box-shadow: var(--shadow-light);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    cursor: pointer;
    border: 2px solid transparent;
}

.feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--success-color));
    transform: scaleX(0);
    transition: var(--transition);
}

.feature-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-heavy);
    border-color: var(--primary-light);
}

.feature-card:hover::before {
    transform: scaleX(1);
}

.feature-card.team-chat:hover {
    border-color: var(--primary-light);
}

.feature-card.whatsapp:hover {
    border-color: rgba(37, 211, 102, 0.3);
}

.feature-card.history:hover {
    border-color: var(--warning-color);
}

.feature-icon {
    width: 64px;
    height: 64px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    font-size: 28px;
    color: var(--white);
}

.feature-card.team-chat .feature-icon {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
}

.feature-card.whatsapp .feature-icon {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
}

.feature-card.history .feature-icon {
    background: linear-gradient(135deg, var(--warning-color) 0%, #e9ab2e 100%);
}

.feature-content h3 {
    font-size: 20px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 8px 0;
}

.feature-content p {
    font-size: 14px;
    color: var(--text-secondary);
    margin: 0;
    line-height: 1.5;
}

.feature-arrow {
    position: absolute;
    top: 24px;
    right: 24px;
    width: 32px;
    height: 32px;
    background: var(--light-bg);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    font-size: 14px;
    transition: var(--transition);
}

.feature-card:hover .feature-arrow {
    background: var(--primary-color);
    color: var(--white);
    transform: translateX(4px);
}

/* Quick Actions */
.quick-actions {
    display: flex;
    justify-content: center;
    gap: 16px;
    flex-wrap: wrap;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 32px;
    border: none;
    border-radius: var(--radius-md);
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    min-width: 180px;
    justify-content: center;
}

.action-btn.primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: var(--white);
    box-shadow: var(--shadow-medium);
}

.action-btn.primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-heavy);
}

.action-btn.secondary {
    background: var(--white);
    color: var(--text-secondary);
    border: 2px solid var(--border-color);
}

.action-btn.secondary:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
    transform: translateY(-2px);
}

.action-btn i {
    font-size: 18px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .welcome-state {
        padding: 24px 16px;
    }
    
    .welcome-title {
        font-size: 36px;
    }
    
    .welcome-description {
        font-size: 18px;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .feature-card {
        padding: 24px 20px;
    }
    
    .quick-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .action-btn {
        width: 100%;
        max-width: 300px;
    }
}

@media (max-width: 480px) {
    .icon-circle {
        width: 100px;
        height: 100px;
    }
    
    .icon-circle i {
        font-size: 36px;
    }
    
    .welcome-title {
        font-size: 28px;
    }
    
    .welcome-description {
        font-size: 16px;
    }
}
</style>
