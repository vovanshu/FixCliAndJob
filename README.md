FixCliAndJob (module for Omeka S)
==========================


Installation
------------

Uncompress files and rename module folder "FixCliAndJob".

See general end user documentation for [Installing a module].


Usage
-----

Fix Cli And Job if function proc_* and exec don`t allowed

config/module.config.php` of the module into your `config/local.config.php`:
...
    'FixCliAndJob' => [
        'config' => [
            'executeJob' => 'execute',  \\ method of performing the job, execute/CRON/CURL
            'time_limit' => 600,
            'CRON_Jobs_limit' => 3 \\ number of jobs to be completed in one event
        ]
    ]
...

Test Job
    https://example.org/admin/testing-loop-job[/:loop][/:timeout

Examples for perform jobs by cron
// */5 * * * * wget -q -O /dev/null "https://example.org/perform-jobs" > /dev/null 2>&1
// */5 * * * * curl --silent "https://example.org/perform-jobs" > /dev/null 2>&1
// */5 * * * * php -q "/path-to-destination/modules/FixCliAndJob/perform-jobs.php" /dev/null 2>&1

TODO
----

[ ] CURL method of performing the job 


Warning
-------

Use it at your own risk.

It’s always recommended to backup your files and your databases and to check
your archives regularly so you can roll back if needed.


Troubleshooting
---------------

See online issues on the [module issues] page on GitHub.


License
-------

This module is published under the [GNU/GPL] license.

As a counterpart to the access to the source code and rights to copy, modify and
redistribute granted by the license, users are provided only with a limited
warranty and the software’s author, the holder of the economic rights, and the
successive licensors have only limited liability.

In this respect, the user’s attention is drawn to the risks associated with
loading, using, modifying and/or developing or reproducing the software by the
user in light of its specific status of free software, that may mean that it is
complicated to manipulate, and that also therefore means that it is reserved for
developers and experienced professionals having in-depth computer knowledge.
Users are therefore encouraged to load and test the software’s suitability as
regards their requirements in conditions enabling the security of their systems
and/or data to be ensured and, more generally, to use and operate it in the same
conditions as regards security.

The fact that you are presently reading this means that you have had knowledge
of the license and that you accept its terms.


Copyright
---------

* Copyright Volodimir Shumeyko, 2024 (see [vovanshu] on GitHub)


[FixCliAndJob]: https://github.com/vovanshu/FixCliAndJob
[Omeka S]: https://omeka.org/s
[Installing a module]: https://omeka.org/s/docs/user-manual/modules/#installing-modules
[module issues]: https://gitlab.com/Daniel-KM/Omeka-S-module-Group/-/issues
[GNU/GPL]: https://www.gnu.org/licenses/gpl-3.0.html
[vovanshu]: https://github.com/vovanshu "Volodimir Shumeyko"
