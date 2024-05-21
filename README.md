# CigBurger Request Project

## ❓ What is this?

⚠️ Warning: This repository contains only a third of the course project. For the complete project, please check out CigBurger Backoffice and CigBurger Kitchen in my profile.

This is my version of the CigBurger Request project built in the course "[2024] CodeIgniter 4 - 3 large PROFESSIONAL projects united by APIs | MVC | PHP8 | MySQL | All about one structure!"

## 🛠️ Setup

To set up the project, follow these steps:

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/joaotfrodrigues/cigburger_request.git
2. **Navigate to the Project Directory:**
    ```bash
    cd cigburger_request
3. **Install Dependencies:**
    ```bash
    composer install
4. **Configure Environment Variables:**
    - Update the .env and Constants.php files with your settings.
5. **Start the Development Server:**
    ```bash
    php spark serve
6. **Access the Application:**
    - Open your web browser and go to http://localhost/cigburger_request/public/

## ⚙️ Important Configuration
- **API_IMAGES_URL:**
    - Ensure you update the **API_IMAGES_URL** constant in the configuration file to match the URL of your CigBurger Backoffice. This constant is crucial for linking to images hosted on the backoffice server.
    - You can find this constant in **app\Config\Constants.php**
        ```bash
        define('API_IMAGES_URL', 'http://your-backoffice-url.com/path/to/images/');
        ```
    - Replace 'http://your-backoffice-url.com/path/to/images/' with the actual URL of your backoffice server.

By following these steps, you should have your CigBurger Request project set up and ready to use. Make sure to check out the other parts of the project, CigBurger Backoffice and CigBurger Kitchen, to complete the full course project.