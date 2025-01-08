<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Footer Styles */
        footer {
            position: relative;
            width: 100%;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(100%);      
            -webkit-backdrop-filter: blur(50px); 
            color: #4a4a4a;                     
            
            padding: 24px 0;        
            text-align: center;
        }

        /* Container to limit content width */
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 16px; /* side padding */
        }

        /* Company Info */
        .company-info {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        /* Contact Section */
        .contacts {
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: center;
            margin-bottom: 12px;
        }
        /* Adjust for wider screens */
        @media (min-width: 600px) {
            .contacts {
                flex-direction: row;
                justify-content: center;
            }
        }

        /* Footer Links */
        .footer-links {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            margin-top: 8px;
        }

        /* Link Styles */
        a {
            color: #4a4a4a;
            text-decoration: none;
            transition: color 0.2s ease-in-out;
        }

        a:hover {
            color: #2e2e2e;
        }
    </style>


    <footer>
        <div class="footer-container">
            <!-- Company Info -->
            <p class="company-info">
                &copy; {{ date('Y') }} Atasan Restu Technology Sdn Bhd.
            </p>

            <!-- Contact Links -->
            <div class="contacts">
                <div>
                    <a href="mailto:atasanrestu@corporate.com">atasanrestu@corporate.com</a>
                </div>
                <div>
                    <a href="tel:+6047304238">+6047304238</a>
                </div>
            </div>

            <!-- Policy / Terms Links -->
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
        </div>
    </footer>
</body>
</html>
