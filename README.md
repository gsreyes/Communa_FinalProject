# Communa: Condominium Services and Billing Management Web-Based System

## About
Communa is built with:
- Laravel 13
- PHP 8.3^.
- Blade
- Breeze
- Tailwind CSS
- MySQL
- XAMPP

## Setup
1. Clone the repository 
2. In your terminal, go the directory of the cloned repository | cd "path"
3. Install PHP dependencies | composer install
4. Setup your environment file referencing the .env.example file
5. Update the database credentials in your .env file
6. In your terminal, run: 
   - php artisan key:generate  
   - php artisan migrate
   - php artisan db:seed (to login as an admin)
   - npm install
   - npm run dev
   - php artisan serve


7. Payment Gateway Setup (via HitPay Sandbox):
    - Create a HitPay SandBox account (guide here:https://docs.hitpayapp.com/apis/guide/sandbox)
    - Create a Ngrok account (guide here: https://ngrok.com/download/windows?tab=install_winget)
    - Setup your environment file using the URLs and API keys generated on HitPay Sandbox and Ngrok temporary URL
    - Add the Ngrok temporary URL + route path (/webhook/hitpay) in the HitPay Sandbox webhook
