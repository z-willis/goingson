First, create your user table.
```
CREATE TABLE user(
userid int not null,
username varchar(255),
password varchar(255),
email varchar(255),
primary key(userid)
);
```

Change userid to A_I


Then, create the event_types table.
```
CREATE TABLE event_types(
id int not null,
name text,
primary key(id)
);
```

Change id to A_I

Next, create the events table.
```
CREATE TABLE events(
eventid int not null,
title varchar(255),
description varchar(255),
latitude varchar(255),
longitude varchar(255),
votes int,
userid int,
typeid int,
primary key(eventid),
foreign key(userid) references user(userid),
foreign key(typeid) references event_types(id)
);
```

Change eventid to A_I


Finally, create the voting table.
```
CREATE TABLE voting(
userid int,
eventid int,
primary key(userid, eventid),
foreign key(userid) references user(userid),
foreign key(eventid) references events(eventid)
);
```


Be sure to populate the event_types table:
```
INSERT INTO event_types (name)
Values ("Event");

INSERT INTO event_types (name)
Values ("Question");
```

For the timer to work, to new columns need to be added the events table:
- duration -> which is an int and its default value is NULL
- endDate -> which is text and its default value is NULL