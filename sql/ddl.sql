--section table
create table mzpc_section(
	id int not null auto_increment,
	primary key (id)
);

--project table
create table mzpc_project(
	id int not null auto_increment,
	name varchar(20),
	status varchar(20),
	section_id int not null,
	team_id int not null,
	primary key (id),
	foreign key (section_id) references mzpc_section(id),
	foreign key (team_id) references mzpc_team(id)
);

--team table
create table mzpc_team(
	id int not null auto_increment,
	name varchar(20),
	project_id int not null,
	score int,
	primary key (id),
	foreign key (project_id) references mzpc_project(id)
);

--write_in table
create table mzpc_write_in(
	id int not null auto_increment,
	message varchar(150),
	member int not null,
	primary key (id),
	foreign key (member) references mzpc_user(id)
);

--user table
create table mzpc_user(
	id int not null auto_increment,
	name varchar(50),
	pass varchar(20),
	primary key (id)
);

--user_team table
create table mzpc_user_team(
	member int not null,
	foreign key (member) references mzpc_user(id)
);