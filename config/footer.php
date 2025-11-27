<style>
    /* Style Footer */
    footer {
        background-color:rgb(0, 0, 0);
        border-top: 1px solid #333;
        color: #888;
        padding: 40px 20px;
        text-align: center;
        margin-top: auto; 
        font-size: 13px;
    }
    
    .footer-content {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }

    .footer-links {
        display: flex;
        gap: 20px;
    }

    .footer-links a {
        color: #ccc;
        text-decoration: none;
        font-weight: bold;
        text-transform: uppercase;
        transition: 0.3s;
    }
    
    .footer-links a:hover { color: #ff4655; }

    .social-icons {
        font-size: 18px;
        letter-spacing: 10px;
        margin-top: 10px;
    }

    .riot-copy {
        margin-top: 20px;
        font-size: 11px;
        opacity: 0.6;
        line-height: 1.5;
        max-width: 600px;
    }
</style>

<footer>
    <div class="footer-content">
        <div style="opacity: 0.5; margin-bottom: 10px;">
            <img src="image/logoValo.png" width="40">
        </div>

        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Support</a>
            <a href="#">Esports API</a>
        </div>

        <div class="social-icons">
            <span>📷 🐦 ▶️ 👾</span>
        </div>

        <div class="riot-copy">
            &copy; 2025 VCT Pacific Fanmade Project. Riot Games, Valorant, and all associated properties are trademarks or registered trademarks of Riot Games, Inc. Created for educational purposes.
        </div>
    </div>
</footer>