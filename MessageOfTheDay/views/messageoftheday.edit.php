<?php

/**
 * @var array $data
 * Zabbix 6.4 compatible view — uses CFormList + addRow()
 */

$form = (new CForm('post', 'zabbix.php'))
    ->addVar('action', 'messageoftheday.save')
    ->setName('messageoftheday_form');

// Warning if config file is not writable
if (!$data['config_writable']) {
    $form->addItem(
        (new CDiv(
            _('Warning: config.json is not writable by the web server. Changes cannot be saved.')
        ))->addClass(ZBX_STYLE_RED)->addStyle('margin-bottom: 8px;')
    );
}

$form_list = (new CFormList())
    // ---- Enabled ----
    ->addRow(
        (new CLabel(_('Enabled'), 'enabled')),
        (new CCheckBox('enabled', 1))
            ->setChecked($data['enabled'])
    )
    // ---- Message ----
    ->addRow(
        (new CLabel(_('Message'), 'message'))->setAsteriskMark(),
        (new CTextArea('message', $data['message']))
            ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
            ->setAttribute('rows', 4)
            ->setAttribute('maxlength', 2048)
    )
    // ---- Banner type ----
    ->addRow(
        (new CLabel(_('Banner type'), 'type')),
        (new CSelect('type'))
            ->setFocusableElementId('type')
            ->addOptions(CSelect::createOptionsFromArray([
                'info'    => _('Information (blue)'),
                'warning' => _('Warning (yellow)'),
                'danger'  => _('Critical (red)'),
                'success' => _('Success (green)')
            ]))
            ->setValue($data['type'])
    )
    // ---- Dismissible ----
    ->addRow(
        (new CLabel(_('Dismissible'), 'dismissible')),
        (new CCheckBox('dismissible', 1))
            ->setChecked($data['dismissible'])
    )
    // ---- Show to ----
    ->addRow(
        (new CLabel(_('Show to'), 'show_to')),
        (new CSelect('show_to'))
            ->setFocusableElementId('show_to')
            ->addOptions(CSelect::createOptionsFromArray([
                'all'    => _('All users'),
                'admins' => _('Administrators only')
            ]))
            ->setValue($data['show_to'])
    );

$form->addItem(
    (new CTabView())
        ->addTab('motd_tab', _('Banner settings'), $form_list)
);

$form->addItem(
    (new CFormActions())
        ->addItem(new CSubmit('update', _('Update')))
);

(new CHtmlPage())
    ->setTitle(_('Message of the Day'))
    ->setDocUrl('')
    ->addItem($form)
    ->show();
