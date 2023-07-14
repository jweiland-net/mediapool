..  include:: /Includes.rst.txt


..  _upgrade:

=======
Upgrade
=======

If you update EXT:mediapool to a newer version, please read this section
carefully!

Update to Version 3.0.0
=======================

We have removed some properties from Scheduler Task classes. Please test,
if your tasks are still running. If not, you have to remove the task and
create it again.

We have migrated the file ending `ts` to `typoscript`. Please update
your references, if you make use of the old file endings.
