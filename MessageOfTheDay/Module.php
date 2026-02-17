<?php

namespace Modules\MessageOfTheDay;

use Zabbix\Core\CModule,
    APP,
    CMenuItem;

class Module extends CModule {

    public function init(): void {
        // Add settings link under Administration menu (visible to Super Admins only)
        APP::Component()->get('menu.main')
            ->findOrAdd(_('Administration'))
                ->getSubmenu()
                    ->add((new CMenuItem(_('Message of the Day')))
                        ->setAction('messageoftheday.edit')
                    );
    }
}
