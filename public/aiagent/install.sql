SOURCE schema.sql;
INSERT INTO crawl_sources(name,url,max_depth,created_at,updated_at) VALUES
('Dawn contact page','https://www.dawn.com/contact/',1,NOW(),NOW()),
('Jang e-paper home','https://e.jang.com.pk/',2,NOW(),NOW());
INSERT INTO campaigns(brand,subject,body_html,body_text,enabled,created_at,updated_at) VALUES
('idlfx.com','IDLFX — partnership enquiry','<p>Hello {{name}},</p><p>This is a message from IDLFX. Please review our website and contact us if you would like more information.</p><p><a href="https://idlfx.com">Visit IDLFX</a></p><p>To stop receiving messages, reply with "unsubscribe".</p>','Hello {{name}},\n\nThis is a message from IDLFX.\nVisit: https://idlfx.com\n\nTo stop receiving messages, reply with "unsubscribe".',0,NOW(),NOW()),
('idlmatch.com','IDLMatch — partnership enquiry','<p>Hello {{name}},</p><p>This is a message from IDLMatch. Please review our website and contact us if you would like more information.</p><p><a href="https://idlmatch.com">Visit IDLMatch</a></p><p>To stop receiving messages, reply with "unsubscribe".</p>','Hello {{name}},\n\nThis is a message from IDLMatch.\nVisit: https://idlmatch.com\n\nTo stop receiving messages, reply with "unsubscribe".',0,NOW(),NOW());
