CREATE TABLE IF NOT EXISTS blog_db.posts (
  id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title varchar(255) NOT NULL,
  content text
);
CREATE TABLE IF NOT EXISTS blog_db.comments (
  id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  post_id int(10) UNSIGNED,
  name varchar(255), content varchar(255)
);
CREATE TABLE IF NOT EXISTS blog_db.tags (
  id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name varchar(255) NOT NULL
);
CREATE TABLE IF NOT EXISTS blog_db.posts_tags (
  post_id int(10) UNSIGNED,
  tag_id int(10) UNSIGNED,
  PRIMARY KEY(post_id, tag_id)
);

INSERT IGNORE INTO blog_db.posts (title, content) VALUES ('記事1', '内容'), ('記事2', '内容'), ('記事3', '内容\n内容'), ('記事4', '内容');
INSERT IGNORE INTO blog_db.tags (name) VALUES ('News'), ('Column');
INSERT IGNORE INTO blog_db.posts_tags VALUES (1, 1), (3, 1), (3, 2);
