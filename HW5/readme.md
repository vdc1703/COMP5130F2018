**Chuong Vu**

**Assignment 5**


I got the index.php which will get all the users from database. When you click the on the user, it will directly you to the user Image Gallery link. This will be dynamic link so I don't need to worry how to point to each user.

Ex: http://weblab.cs.uml.edu/~cvu/COMP5130/HW5//users/user.php?id=user1
	http://weblab.cs.uml.edu/~cvu/COMP5130/HW5//users/user.php?id=user2

For each user, I be able to get all the file from the folders. My goal next is get only the image file on each table and build a tree for all the file. My consider now is how do I store the tree? 

- I'm thinking of storing every link of image to the MySQL database but this will grow up fast and I don't think the database can be able to store all like millions of pictures.
- My other idea is create an XML which store the folder tree in each folder. This will be save of my time but this already require the website have to run this every time the weblink is open. This can be take out some resource on the server side too.

I'm still not sure which way I will do. I will do more research to see what is best for the Image Gallery which allow user register account and upload images to website.

