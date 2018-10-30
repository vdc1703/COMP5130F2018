**Chuong Vu**

**Project Report (HW6) 10.30.2018**


- I have succeeded create a homepage. At the homepage, I just limited the size of image is less than 1Mb for now. The upload function somehow is not working on weblab.cs.uml.edu, it’s working find on my localhost using xampp. 

- The login function is working now. I can login with my username and password as Administrator. 

- Settings pages is use for change password and email. Also, for change the gallery to private or public.

- The idea is when you upload images, all the information of the image will be saved in database, right after image uploaded, a thumbnail function will be calling to create image thumbnail. This will use for slide show.

- The reset password is working. It will send a temporary password to your email and waiting for the confirm link is clicked. When the confirm link is clicked, server will check if the url link is correct with the auth_key and then it will update the password (MD5). 

Website
<p align="center"><img src="https://github.com/vdc1703/COMP5130F2018/blob/master/HW6/ResetPass1.JPG" /></p>

In database
<p align="center"><img src="https://github.com/vdc1703/COMP5130F2018/blob/master/HW6/ResetPass2.JPG" /></p>

Email Confirm
<p align="center"><img src="https://github.com/vdc1703/COMP5130F2018/blob/master/HW6/ResetPass3.JPG" /></p>

Link confirmed Ex: http://weblab.cs.uml.edu/~cvu/COMP5130/HW6/users.php?act=lost_password-a&id=00d4bd053fca85cc8c772afd88a3a515

- Register is not working. Somehow CAPTCHA is not working with weblab. I will remove it to make it simple and easy for everyone can register.


**Summary**

Right now, all I did is just a template and basic functions for Image Gallery. There are many bugs need to fix. Belong that, I plan to use bootstrap to make the website look better and it also can help the website run on Mobile Devices.
