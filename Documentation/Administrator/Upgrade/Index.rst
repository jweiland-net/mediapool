..  include:: /Includes.rst.txt


..  _upgrade:

=======
Upgrade
=======

If you update EXT:mediapool to a newer version, please read this section
carefully!

Update to Version 5.0.1
=======================

Important Database Schema Change
--------------------------------

In version 5.0.1 of the MediaPool extension, a new `uid` column has been added
to the table `tx_mediapool_playlist_video_mm`. This change ensures better
integration with TYPO3's data handling mechanisms and improves future
extensibility.

However, this schema change cannot be handled automatically by TYPO3's
Install Tool or Database Compare utility. The reason is that MySQL and MariaDB
require the `AUTO_INCREMENT` attribute and the `PRIMARY KEY` definition to be
applied **in a single SQL statement**. TYPO3's default approach executes these
operations separately, which leads to SQL errors on these platforms.

To address this, an **Upgrade Wizard** has been included that performs this
change safely and correctly across supported database systems:

*   On **MySQL** and **MariaDB**, the `uid` column is added as the first
    column in the table using the `FIRST` clause.
*   On **PostgreSQL** and **SQLite**, the column is added at the end of the
    table, as those systems do not support column positioning.

..  note::
    This Upgrade Wizard **cannot be executed via the TYPO3 Install Tool**,
    because it depends on the presence of the `uid` column to satisfy internal
    upgrade prerequisites. **You must run this wizard from the CLI** using:

    ..  code-block:: bash
        vendor/bin/typo3 upgrade:run addUidAsAutoIncrementToMediapoolReferenceTable

    Once the wizard has been executed, the database schema will be fully
    compatible with the updated extension version.

Update to Version 3.0.0
=======================

We have removed some properties from Scheduler Task classes. Please test,
if your tasks are still running. If not, you have to remove the task and
create it again.

We have migrated the file ending `ts` to `typoscript`. Please update
your references, if you make use of the old file endings.
