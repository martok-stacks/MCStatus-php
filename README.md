MCStatus - Minecraft Server Status
==================================

Also see: [Python mcstatus](https://github.com/Dinnerbone/mcstatus) by Dinnerbone.

Usage
-----------
See `example_status.php` for real-world code.

Generally though, that's all:

    $mcs = new MCStatus($host, $port);
    $stuff = $mcs->getFull();

The elements of "stuff" may be subject to change and so are not documented here.
[http://wiki.vg/Query](http://wiki.vg/Query) has a list though.

Rights
-----------
CC BY-NC-SA 3.0

Clarification:

* NC refers to the code itself, not what you build around it (e.g.
  ad-sponsored sites are o.k.)
* to satisfy BY, simply link to the project's [GitHub](https://github.com/martok/MCStatus-php) page.