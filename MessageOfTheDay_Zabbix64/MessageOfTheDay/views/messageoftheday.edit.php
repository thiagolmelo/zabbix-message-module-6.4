<?php

/**
 * @var array $data
 */

$form = (new CForm('post', 'zabbix.php'))
    ->addVar('action', 'messageoftheday.save')
    ->setName('messageoftheday_form');

// Warning if config file is not writable
if (!$data['config_writable']) {
    $form->addItem(
        (new CTag('div', true,
            _('Warning: config.json is not writable by the web server. Changes cannot be saved.')
        ))->addClass(ZBX_STYLE_RED)
    );
}

// ---- Enabled ----
$enabled_field = (new CFormField(
    (new CCheckBox('enabled', 1))
        ->setLabel(_('Enabled'))
        ->setChecked($data['enabled'])
))
->setLabel(_('Enabled'));

// ---- Message text ----
$message_field = (new CFormField(
    (new CTextArea('message', $data['message']))
        ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
        ->setAttribute('rows', 4)
        ->setAttribute('maxlength', 2048)
        ->setAriaRequired()
))
->setLabel(_('Message'))
->setRequired();

// ---- Banner type ----
$type_field = (new CFormField(
    (new CSelect('type'))
        ->setFocusableElementId('type')
        ->addOptions(CSelect::createOptionsFromArray([
            'info'    => _('Information (blue)'),
            'warning' => _('Warning (yellow)'),
            'danger'  => _('Critical (red)'),
            'success' => _('Success (green)')
        ]))
        ->setValue($data['type'])
))
->setLabel(_('Banner type'));

// ---- Dismissible ----
$dismissible_field = (new CFormField(
    (new CCheckBox('dismissible', 1))
        ->setLabel(_('Allow users to dismiss'))
        ->setChecked($data['dismissible'])
))
->setLabel(_('Dismissible'));

// ---- Show to ----
$show_to_field = (new CFormField(
    (new CSelect('show_to'))
        ->setFocusableElementId('show_to')
        ->addOptions(CSelect::createOptionsFromArray([
            'all'    => _('All users'),
            'admins' => _('Administrators only')
        ]))
        ->setValue($data['show_to'])
))
->setLabel(_('Show to'));

$fieldset = (new CFormGrid())
    ->addItem($enabled_field)
    ->addItem($message_field)
    ->addItem($type_field)
    ->addItem($dismissible_field)
    ->addItem($show_to_field);

$form->addItem($fieldset);

$form->addItem(
    (new CFormActions(
        (new CSubmit('update', _('Update')))->setId('update')
    ))
);

(new CHtmlPage())
    ->setTitle(_('Message of the Day'))
    ->setDocUrl('')
    ->addItem($form)
    ->show();
