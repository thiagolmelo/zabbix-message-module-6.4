<?php

namespace Modules\MessageOfTheDay;

use Zabbix\Core\CModule,
    APP,
    CMenuItem,
    CWebUser;

class Module extends CModule {

    public function init(): void {
        APP::Component()->get('menu.main')
            ->findOrAdd(_('Administration'))
                ->getSubmenu()
                    ->add((new CMenuItem(_('Message of the Day')))
                        ->setAction('messageoftheday.edit')
                    );

        $config = $this->loadConfig();

        if ($config['enabled'] && !empty($config['message'])) {
            $user_type = CWebUser::getType();
            $show = false;
            if ($config['show_to'] === 'all') {
                $show = true;
            } elseif ($config['show_to'] === 'admins' && $user_type >= USER_TYPE_ZABBIX_ADMIN) {
                $show = true;
            }

            if ($show) {
                $js_config = json_encode([
                    'enabled'     => true,
                    'message'     => $config['message'],
                    'type'        => $config['type'],
                    'dismissible' => (bool) $config['dismissible'],
                    'link_url'    => $config['link_url'] ?? '',
                    'link_label'  => !empty($config['link_label']) ? $config['link_label'] : 'Read more'
                ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);

                zbx_add_post_js('window.MOTD_CONFIG = ' . $js_config . ';');
            }
        }
    }

    private function loadConfig(): array {
        $defaults = [
            'enabled'     => false,
            'message'     => '',
            'type'        => 'info',
            'dismissible' => true,
            'show_to'     => 'all',
            'link_url'    => '',
            'link_label'  => 'Read more'
        ];

        $config_file = __DIR__ . '/config.json';
        if (file_exists($config_file)) {
            $data = json_decode(file_get_contents($config_file), true);
            if (is_array($data)) {
                return array_merge($defaults, $data);
            }
        }

        return $defaults;
    }
}
