**Chuong Vu**

**Week 4**

Weblab link: http://weblab.cs.uml.edu/~cvu/COMP5130/Week-4/root/

**Completed**
I got the index.php which will get all the users from database. When you click the on the user, it will directly you to the user Image Gallery link. This will be dynamic link so I don't need to worry how to point to each user.

Ex: http://weblab.cs.uml.edu/~cvu/COMP5130/HW5//users/user.php?id=user1 http://weblab.cs.uml.edu/~cvu/COMP5130/HW5//users/user.php?id=user2


**Problem**
I had a hard time to config the MySQL on the Weblab server since there are no User Interface like XAMMP. I have to work with CS Helpdesk to help me out how to use the command line to create and import database.


**Problem Solving**
I was able to get the user from database that I created. Also, user page is created dynamic using only user.php and user ID.

I still playing with PHP and dymanic feature that PHP bring here. However, what I see is this is too simple and the source code will not work for a bigger project. I have to redesign the database and also I will based on some template that I see on the internet to help me out.

**Next**

For each user, I be able to get all the file from the folders. My goal next is get only the image file on each table and build a tree for all the file. My consider now is how do I store the tree?

- I'm thinking of storing every link of image to the MySQL database but this will grow up fast and I don't think the database can be able to store all like millions of pictures.
- My other idea is create an XML which store the folder tree in each folder. This will be save of my time but this already require the website have to run this every time the weblink is open. This can be take out some resource on the server side too.

I'm still not sure which way I will do. I will do more research to see what is best for the Image Gallery which allow user register account and upload images to website.