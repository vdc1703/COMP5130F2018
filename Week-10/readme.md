**Chuong Vu**

**Week 10**


Weblab link: http://weblab.cs.uml.edu/~cvu/COMP5130/Week-10/root/


**Completed**

- Upload image (max size to 10MB)
- User Control Pannel
- Create Album
- Move image to Album

HomePage

<p align="center"><img src="https://github.com/vdc1703/COMP5130F2018/blob/master/Week-10/images/homepage.JPG" /></p>

Database Album

<p align="center"><img src="https://github.com/vdc1703/COMP5130F2018/blob/master/Week-10/images/database_album.JPG" /></p>


**Problem**

For the upload file to the server. It only limited to max size is 2MB. 

**Problem Solving**

upload_max_filesize=2M to upload_max_filesize=10

At first I thought my code is wrong but after few days troubleshoot. I know that I need to config the PHP server.
I change the upload_max_filesize=2M to upload_max_filesize=10M in php.ini so the problem solved

**Next**

- Will be add Slide Show
- Check Admin/Member user


**Note**

Somehow on Weblab do not allow user to use .htaccess. The website works fine on my localhost host but I got "Forbidden" on the Weblab due to .htaccess.
