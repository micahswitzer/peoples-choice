-- section
create table mzpc_section(
	id int not null auto_increment,
	primary key (id)
);

-- project
create table mzpc_project(
	id int auto_increment,
	name varchar(25),
	status_open boolean,
	section_id int not null,
	primary key (id),
	foreign key (section_id) references mzpc_section(id)
);

-- user
create table mzpc_user(
	id int auto_increment,
	name varchar(40),
	pass varchar(25),
	student boolean,
	admin boolean,
	primary key (id)
);

-- team
create table mzpc_team(
	id int auto_increment,
	name varchar(25),
	project_id int not null,
	primary key (id),
	foreign key (project_id) references mzpc_project(id)
);

-- write_in
create table mzpc_write_in(
	id int auto_increment,
	message varchar(150),
	user_id int not null,
	team_id int not null,
	primary key (id),
	foreign key (user_id) references mzpc_user(id),
	foreign key (team_id) references mzpc_team(id)
);

-- score
create table mzpc_score(
	id int auto_increment,
	user_id int not null,
	team_id int not null,
	primary key (id),
	foreign key (user_id) references mzpc_user(id),
	foreign key (team_id) references mzpc_team(id)
);

-- member
create table mzpc_member(
	user_id int not null,
	team_id int not null,
	foreign key (user_id) references mzpc_user(id),
	foreign key (team_id) references mzpc_team(id)
);
