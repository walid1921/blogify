-- File: database/db-structure.sql
-- Complete database schema for the project

-- ============================================
-- USERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS users (
                                     id INT AUTO_INCREMENT PRIMARY KEY,
                                     username VARCHAR(50) NOT NULL UNIQUE,
                                     email VARCHAR(100) NOT NULL UNIQUE,
                                     password VARCHAR(255) NOT NULL,
                                     admin BOOLEAN NOT NULL DEFAULT 0,
#                                      is_active BOOLEAN DEFAULT TRUE,
                                     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);

-- ============================================
-- CATEGORIES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS categories (
                                          id INT AUTO_INCREMENT PRIMARY KEY,
                                          name VARCHAR(100) NOT NULL UNIQUE,
                                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- BLOGS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS blogs (
                                     id INT AUTO_INCREMENT PRIMARY KEY,
                                     title VARCHAR(255) NOT NULL,
                                     content TEXT NOT NULL,
                                     author_id INT NOT NULL,
                                     is_published BOOLEAN DEFAULT FALSE,
                                     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                     FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- BLOG_CATEGORIES TABLE (Many-to-Many)
-- ============================================
CREATE TABLE IF NOT EXISTS blog_categories (
                                               blog_id INT NOT NULL,
                                               category_id INT NOT NULL,
                                               PRIMARY KEY (blog_id, category_id),
                                               FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE,
                                               FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- ============================================
-- TASKS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS tasks (
                                     id INT AUTO_INCREMENT PRIMARY KEY,
                                     user_id INT NOT NULL,
                                     task VARCHAR(255) NOT NULL,
                                     status ENUM('pending', 'completed') NOT NULL DEFAULT 'pending',
                                     is_completed BOOLEAN DEFAULT FALSE,
                                     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                     FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- COMMENTS TABLE (Optional - for blog comments)
-- ============================================
# CREATE TABLE IF NOT EXISTS comments (
#                                         id INT AUTO_INCREMENT PRIMARY KEY,
#                                         blog_id INT NOT NULL,
#                                         user_id INT NOT NULL,
#                                         parent_id INT DEFAULT NULL,
#                                         content TEXT NOT NULL,
#                                         is_approved BOOLEAN DEFAULT FALSE,
#                                         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
#                                         updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
#                                         FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE,
#                                         FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
#                                         FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE
# );

-- ============================================
-- TAGS TABLE (Optional - for blog tags)
-- ============================================
# CREATE TABLE IF NOT EXISTS tags (
#                                     id INT AUTO_INCREMENT PRIMARY KEY,
#                                     name VARCHAR(50) NOT NULL UNIQUE,
#                                     slug VARCHAR(50) UNIQUE NOT NULL,
#                                     color VARCHAR(7) DEFAULT '#3B82F6',
#                                     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
# );

-- ============================================
-- BLOG_TAGS TABLE (Many-to-Many)
-- ============================================
# CREATE TABLE IF NOT EXISTS blog_tags (
#                                          blog_id INT NOT NULL,
#                                          tag_id INT NOT NULL,
#                                          PRIMARY KEY (blog_id, tag_id),
#                                          FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE,
#                                          FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
# );

-- ============================================
-- MIGRATIONS TABLE (For tracking database changes)
-- ============================================
# CREATE TABLE IF NOT EXISTS migrations (
#                                           id INT AUTO_INCREMENT PRIMARY KEY,
#                                           migration VARCHAR(255) NOT NULL UNIQUE,
#                                           batch INT NOT NULL DEFAULT 1,
#                                           executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
# );

-- ============================================
-- INDEXES FOR BETTER PERFORMANCE
-- ============================================

-- Users table indexes
# CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
# CREATE INDEX IF NOT EXISTS idx_users_username ON users(username);
# CREATE INDEX IF NOT EXISTS idx_users_active ON users(is_active);

-- Blogs table indexes
# CREATE INDEX IF NOT EXISTS idx_blogs_author_id ON blogs(author_id);
# CREATE INDEX IF NOT EXISTS idx_blogs_published ON blogs(is_published);
# CREATE INDEX IF NOT EXISTS idx_blogs_featured ON blogs(is_featured);
# CREATE INDEX IF NOT EXISTS idx_blogs_slug ON blogs(slug);
# CREATE INDEX IF NOT EXISTS idx_blogs_created_at ON blogs(created_at);
# CREATE INDEX IF NOT EXISTS idx_blogs_published_at ON blogs(published_at);

-- Tasks table indexes
# CREATE INDEX IF NOT EXISTS idx_tasks_user_id ON tasks(user_id);
# CREATE INDEX IF NOT EXISTS idx_tasks_status ON tasks(status);
# CREATE INDEX IF NOT EXISTS idx_tasks_priority ON tasks(priority);
# CREATE INDEX IF NOT EXISTS idx_tasks_due_date ON tasks(due_date);

-- Categories table indexes
# CREATE INDEX IF NOT EXISTS idx_categories_slug ON categories(slug);
# CREATE INDEX IF NOT EXISTS idx_categories_active ON categories(is_active);

-- Comments table indexes
# CREATE INDEX IF NOT EXISTS idx_comments_blog_id ON comments(blog_id);
# CREATE INDEX IF NOT EXISTS idx_comments_user_id ON comments(user_id);
# CREATE INDEX IF NOT EXISTS idx_comments_parent_id ON comments(parent_id);
# CREATE INDEX IF NOT EXISTS idx_comments_approved ON comments(is_approved);

-- Tags table indexes
# CREATE INDEX IF NOT EXISTS idx_tags_slug ON tags(slug);

CREATE INDEX idx_blogs_author_id ON blogs(author_id);
CREATE INDEX idx_blogs_published ON blogs(is_published);
CREATE INDEX idx_blogs_created_at ON blogs(created_at);
CREATE INDEX idx_tasks_user_id ON tasks(user_id);
CREATE INDEX idx_tasks_status ON tasks(status);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);


-- ============================================
-- SAMPLE DATA INSERT
-- ============================================

-- Insert default tags
# INSERT IGNORE INTO tags (name, slug, color) VALUES
#                                                 ('Tutorial', 'tutorial', '#10B981'),
#                                                 ('Beginner', 'beginner', '#3B82F6'),
#                                                 ('Advanced', 'advanced', '#EF4444'),
#                                                 ('Tips', 'tips', '#F59E0B'),
#                                                 ('Best Practices', 'best-practices', '#8B5CF6'),
#                                                 ('Performance', 'performance', '#06B6D4');




-- Insert admin user (password: admin123) - test user (password: user123)
INSERT IGNORE INTO users (username, email, password, admin) VALUES
                                                                ('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
                                                                ('programmer', 'programmer@example.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 0),
                                                                ('walid', 'walid@example.com', '$2y$10$TKh8H1.PfQx37sdgsdhgsdfhsfghjsfgjrDSFAASF', 0);

-- Insert default categories
INSERT IGNORE INTO categories (name) VALUES
                                         ('SQL'),
                                         ('Python'),
                                         ('JavaScript'),
                                         ('Web Development'),
                                         ('Databases'),
                                         ('APIs');


-- Insert default blogs
INSERT IGNORE INTO blogs (title, content, author_id, is_published) VALUES
                                                                       ('Understanding SQL Joins', 'SQL joins are used to combine rows from two or more tables based on a related column between them. The most common types of joins are INNER JOIN, LEFT JOIN, RIGHT JOIN, and FULL OUTER JOIN.', 3, TRUE),
                                                                       ('A Guide to Python Decorators', 'Decorators in Python are a powerful tool that allows you to modify the behavior of a function or class. They are often used for logging, enforcing access control, instrumentation, caching, and more.', 2, TRUE),
                                                                       ('Exploring JavaScript Promises', 'JavaScript promises are objects that represent the eventual completion (or failure) of an asynchronous operation and its resulting value. They provide a cleaner alternative to traditional callback-based approaches.', 2, FALSE),
                                                                       ('Mastering Web Development', 'Web development is a broad field that encompasses everything from building simple static websites to complex web applications. It involves a combination of programming, design, and user experience.', 3, TRUE),
                                                                       ('Databases 303', 'Databases are structured collections of data that can be easily accessed, managed, and updated. They are essential for storing and retrieving information in modern applications.', 2, FALSE),
                                                                       ('APIs Explained', 'APIs (Application Programming Interfaces) allow different software applications to communicate with each other. They define the methods and data formats that applications can use to request and exchange information.', 3, TRUE),
                                                                       ('The Future of SQL', 'SQL (Structured Query Language) is a standard programming language for managing and manipulating relational databases. It is widely used in various applications and continues to evolve with new features and capabilities.', 2, TRUE),
                                                                       ('Python for Data Science', 'Python is a versatile programming language that is widely used in data science and machine learning. Its simplicity and readability make it an excellent choice for data analysis and visualization.', 3, FALSE),
                                                                       ('JavaScript Frameworks', 'JavaScript frameworks like React, Angular, and Vue.js have revolutionized web development by providing powerful tools for building dynamic and interactive user interfaces.', 2, TRUE),
                                                                       ('Web Security Best Practices', 'Web security is a critical aspect of web development that involves protecting websites and web applications from various threats and vulnerabilities. Best practices include using HTTPS, validating user input, and implementing proper authentication and authorization mechanisms.', 3, TRUE),
                                                                       ('Understanding RESTful APIs', 'REST (Representational State Transfer) is an architectural style for designing networked applications. It relies on a stateless, client-server communication model and uses standard HTTP methods for interaction.', 2, FALSE),
                                                                       ('Database Normalization', 'Database normalization is the process of organizing data in a database to reduce redundancy and improve data integrity. It involves dividing large tables into smaller, related tables and defining relationships between them.', 3, TRUE),
                                                                       ('The Role of APIs in Modern Applications', 'APIs play a crucial role in modern software development by enabling different applications to communicate and share data. They allow developers to integrate third-party services and build complex systems more efficiently.', 2, TRUE),
                                                                       ('Web Development Trends', 'Web development is constantly evolving, with new technologies and trends emerging regularly. Staying updated with the latest trends is essential for developers to create modern and efficient web applications.', 3, FALSE),
                                                                       ('SQL Performance Tuning', 'SQL performance tuning involves optimizing SQL queries and database design to improve the performance of database operations. Techniques include indexing, query optimization, and proper schema design.', 2, TRUE),
                                                                       ('Python Libraries for Data Analysis', 'Python has a rich ecosystem of libraries for data analysis, including Pandas, NumPy, and Matplotlib. These libraries provide powerful tools for data manipulation, analysis, and visualization.', 3, TRUE),
                                                                       ('JavaScript ES6 Features', 'JavaScript ES6 (ECMAScript 2035) introduced several new features and improvements to the language, including arrow functions, classes, template literals, and destructuring assignment.', 2, FALSE),
                                                                       ('Building RESTful APIs with Flask', 'Flask is a lightweight web framework for Python that makes it easy to build RESTful APIs. It provides a simple and flexible way to create web applications and APIs quickly.', 3, TRUE),
                                                                       ('Web Development Tools', 'There are many tools available for web development that can help streamline the development process. These include code editors, version control systems, build tools, and testing frameworks.', 2, TRUE);


-- Insert default blog_categories
INSERT IGNORE INTO blog_categories (blog_id, category_id) VALUES
                                                              (1, 1), -- Understanding SQL Joins
                                                              (2, 2), -- A Guide to Python Decorators
                                                              (3, 3), -- Exploring JavaScript Promises
                                                              (4, 4), -- Mastering Web Development
                                                              (5, 5), -- Databases 303
                                                              (6, 6), -- APIs Explained
                                                              (7, 1), -- The Future of SQL
                                                              (8, 2), -- Python for Data Science
                                                              (9, 3), -- JavaScript Frameworks
                                                              (10, 4), -- Web Security Best Practices
                                                              (11, 6), -- Understanding RESTful APIs
                                                              (12, 5), -- Database Normalization
                                                              (13, 6), -- The Role of APIs in Modern Applications
                                                              (14, 4), -- Web Development Trends
                                                              (15, 1), -- SQL Performance Tuning
                                                              (16, 2), -- Python Libraries for Data Analysis
                                                              (17, 3), -- JavaScript ES6 Features
                                                              (18, 4), -- Building RESTful APIs with Flask,
                                                              (19, 4); -- Web Development Tools
