<?php

namespace Modules\MessageOfTheDay\Actions;

use CController,
    CControllerResponseData,
    CWebUser;

class MessageOfTheDayEdit extends CController {

    public function init(): void {
        $this->disableCsrfValidation();
    }

    protected function checkInput(): bool {
        return true;
    }

    protected function checkPermissions(): bool {
        return (CWebUser::getType() == USER_TYPE_SUPER_ADMIN);
    }

    protected function doAction(): void {
        $config = $this->loadConfig();

        $response = new CControllerResponseData([
            'enabled'         => (bool) $config['enabled'],
            'message'         => $config['message'] ?? '',
            'type'            => $config['type'] ?? 'info',
            'dismissible'     => (bool) ($config['dismissible'] ?? true),
            'show_to'         => $config['show_to'] ?? 'all',
            'link_url'        => $config['link_url'] ?? '',
            'link_label'      => $config['link_label'] ?? 'Read more',
            'config_writable' => is_writable(__DIR__ . '/../config.json')
        ]);
        $response->setTitle(_('Message of the Day'));
        $this->setResponse($response);
    }

    private function loadConfig(): array {
        $config_file = __DIR__ . '/../config.json';
        if (file_exists($config_file)) {
            $data = json_decode(file_get_contents($config_file), true);
            if (is_array($data)) {
                return $data;
            }
        }
        return [
            'enabled'     => false,
            'message'     => '',
            'type'        => 'info',
            'dismissible' => true,
            'show_to'     => 'all',
            'link_url'    => '',
            'link_label'  => 'Read more'
        ];
    }
}
