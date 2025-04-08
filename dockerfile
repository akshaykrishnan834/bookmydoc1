# Use official PHP image with built-in web server
FROM php:8.1-cli

# Set working directory
WORKDIR /var/www/html

# Copy all project files into the container
COPY . .

# Expose the port Render expects
EXPOSE 10000

# Start PHP's built-in server on Render's internal IP and port
CMD ["php", "-S", "0.0.0.0:10000"]
