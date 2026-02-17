<?php

namespace Modules\MessageOfTheDay\Actions;

use CController,
    CControllerResponseData;

class MessageOfTheDayConfig extends CController {

    public function init(): void {
        $this->disableCsrfValidation();
    }

    protected function checkInput(): bool {
        return true;
    }

    protected function checkPermissions(): bool {
        // Accessible by any logged-in user
        return (bool) $this->getUserType();
    }

    protected function doAction(): void {
        $config = $this->loadConfig();

        $response = new CControllerResponseData([
            'enabled'     => (bool) $config['enabled'],
            'message'     => $config['message'] ?? '',
            'type'        => $config['type'] ?? 'info',
            'dismissible' => (bool) ($config['dismissible'] ?? true),
            'show_to'     => $config['show_to'] ?? 'all',
            'user_type'   => $this->getUserType()
        ]);
        $response->setTitle('');
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
            'show_to'     => 'all'
        ];
    }
}
