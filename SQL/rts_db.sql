create database rts;
use rts;
#basic user information
create table user_info(u_id int not null auto_increment,
uname char(20) not null,
passwd char(20) not null,
primary key(u_id));

#battle information
create table battle(b_id int not null auto_increment,
begin_time timestamp default current_timestamp,
last_time smallint,
destroy_num smallint,
user1_id int,
user2_id int,
winner_id int,
primary key(b_id),
foreign key(user1_id) references user_info(u_id),
foreign key(user2_id) references user_info(u_id));

#rank information
create table rank(u_id int,
play_count int,
win_count int,
primary key(u_id),
foreign key(u_id) references user_info(u_id));


