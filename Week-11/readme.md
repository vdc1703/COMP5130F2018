**Chuong Vu**

**Week 11**


Weblab link: http://weblab.cs.uml.edu/~cvu/COMP5130/Week-11/root/


**Completed**

- Finished the Slide-Show
- Admin and User is now different to view Gallery
    + If users, only allow to create and delete their own Gallery Pictures
    + If Admin, full access. Can create/edit/delete any pictures from all user.
- Fixed .htaccess permission for rewrite http links

**Problem**

- Can not rename images file to unique name after upload
- Can not check for images width > 1024px


**Problem Solving**

- For the rename images file to the database. I use the uniqid() functions ($newfileName = "img_".uniqid().".".$ext;) to make sure every images upload and save to the database is unique.

- With the iamges widtch check, I use the PATHINFO_* to get the picture information.

**Next**

- Bug on delete albums and images. Will need to be fixed in final.
- Re-work on upload. Allow to drag and drop.

