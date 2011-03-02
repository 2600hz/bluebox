Hello,

I developed the directory module for running on a low-powered embedded box.

While I was working on the directory module, the overhead of the Kohana framework, combined with the slow speed of the box in question, caused problems.

Specifically, when someone is looking at the directory, it polls frequently to see which phones are off-hook, and which are not.

When I had several machines polling at the same time, the box got overwhelmed.

So I put in this workaround. It consists of a patch which tells index.php (the main one in the root bluebox directory) to check if the URL being requested is directory/json_listing, and if so, don't use Kohana, but just get the page directly.

Since there are many dependencies, I've written the absolute minimum code to make it work. This has drawbacks - the logger doesn't log, for example, and there is no security. The security shouldn't be an issue, since it's public information (if you want to use it).

If you decide to go ahead and use this, simple rename kohana-circumvent.php.txt to kohana-circumvent.php, and apply the patch to the main index.php. To reverse the process, apply the patch again - it will ask you if you want to reverse it, say yes, and don't forget to rename the php back to php.txt.

To patch, from this directory:

patch ../../../index.php < kohana-circumvent.patch
