<?php

namespace Modules\MessageOfTheDay\Actions;

use CController,
    CControllerResponseRedirect,
    CUrl,
    CWebUser;

class MessageOfTheDaySave extends CController {

    public function init(): void {
        $this->disableCsrfValidation();
    }

    protected function checkInput(): bool {
        $fields = [
            'enabled'     => 'in 0,1',
            'message'     => 'string',
            'type'        => 'in info,warning,danger,success',
            'dismissible' => 'in 0,1',
            'show_to'     => 'in all,admins',
            'link_url'    => 'string',
            'link_label'  => 'string'
        ];

        $ret = $this->validateInput($fields);

        if (!$ret) {
            $this->setResponse(new CControllerResponseRedirect(
                (new CUrl('zabbix.php'))
                    ->setArgument('action', 'messageoftheday.edit')
                    ->setArgument('saved', '0')
                    ->getUrl()
            ));
        }

        return $ret;
    }

    protected function checkPermissions(): bool {
        return (CWebUser::getType() == USER_TYPE_SUPER_ADMIN);
    }

    protected function doAction(): void {
        $link_url = trim($this->getInput('link_url', ''));

        // Basic URL sanitization - only allow http/https
        if ($link_url !== '' && !preg_match('/^https?:\/\//i', $link_url)) {
            $link_url = 'https://' . $link_url;
        }

        $config = [
            'enabled'     => (bool) $this->getInput('enabled', 0),
            'message'     => $this->getInput('message', ''),
            'type'        => $this->getInput('type', 'info'),
            'dismissible' => (bool) $this->getInput('dismissible', 1),
            'show_to'     => $this->getInput('show_to', 'all'),
            'link_url'    => $link_url,
            'link_label'  => trim($this->getInput('link_label', 'Read more')) ?: 'Read more'
        ];

        $config_file = __DIR__ . '/../config.json';
        $success = file_put_contents(
            $config_file,
            json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        $this->setResponse(new CControllerResponseRedirect(
            (new CUrl('zabbix.php'))
                ->setArgument('action', 'messageoftheday.edit')
                ->setArgument('saved', $success !== false ? '1' : '0')
                ->getUrl()
        ));
    }
}
