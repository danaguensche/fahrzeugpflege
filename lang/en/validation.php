<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Das :attribute Feld muss akzeptiert werden.',
    'accepted_if' => 'Das :attribute Feld muss akzeptiert werden, wenn :other den Wert :value hat.',
    'active_url' => 'Das :attribute Feld muss eine gültige URL sein.',
    'after' => 'Das :attribute Feld muss ein Datum nach dem :date beinhalten.',
    'after_or_equal' => 'Das :attribute Feld muss der :date oder ein späteres Datum sein.',
    'alpha' => 'Das :attribute Feld darf nur Buchstaben enthalten.',
    'alpha_dash' => 'Das :attribute Feld darf nur Buchstaben, Zahlen, Bindestriche und Unterstriche enthalten.',
    'alpha_num' => 'Das :attribute Feld darf nur Buchstaben und Zahlen enthalten.',
    'array' => 'Das :attribute Feld muss ein Array sein.',
    'ascii' => 'Das :attribute Feld darf nur 1-Byte-lange alphanumerische Zeichen und Symbole enthalten.',
    'before' => 'Das :attribute Feld muss ein Datum vor dem :date beinhalten.',
    'before_or_equal' => 'Das :attribute Feld muss der :date oder ein vorheriges Datum sein.',
    'between' => [
        'array' => 'Das :attribute Feld darf :min bis :max Einträge enthalten.',
        'file' => 'Das :attribute Feld darf zwischen :min und :max Kilobytes sein.',
        'numeric' => 'Das :attribute Feld muss zwischen :min und :max sein.',
        'string' => 'Das :attribute Feld muss :min bis :max Zeichen haben.',
    ],
    'boolean' => 'Das :attribute Feld muss true (wahr) oder false (falsch) sein.',
    'can' => 'Das :attribute Feld enthält einen unerlaubten Wert.',
    'confirmed' => 'Das Bestätigungsfeld für :attribute stimmt nicht überein.',
    'contains' => 'Dem :attribute Feld fehlt ein Pflichtwert. ',
    'current_password' => 'Das Passwort ist falsch.',
    'date' => 'Das :attribute Feld muss ein gültiges Datum sein.',
    'date_equals' => 'Das Datum muss :date sein.',
    'date_format' => 'Das :attribute Feld muss das Format :format folgen.',
    'decimal' => 'Das :attribute Feld muss :decimal Nachkommastellen haben.',
    'declined' => 'Das :attribute Feld muss abgelehnt sein.',
    'declined_if' => 'Das :attribute Feld muss abgelehnt sein, wenn :other den Wert :value hat.',
    'different' => 'Die Felder :attribute und :other müssen verschieden sein.',
    'digits' => 'Das :attribute Feld darf :digits Ziffern enthalten.',
    'digits_between' => 'Das :attribute Feld darf zwischen :min und :max Ziffern haben.',
    'dimensions' => 'Das :attribute Feld hat ungültige Bildgrößen.',
    'distinct' => 'Das :attribute Feld enthält einen doppelten Wert.',
    'doesnt_end_with' => 'Das :attribute Feld darf nicht enden mit: :values.',
    'doesnt_start_with' => 'Das :attribute Feld darf nicht beginnen mit: :values.',
    'email' => 'Das :attribute Feld muss eine gültige E-Mail-Adresse sein.',
    'ends_with' => 'Das :attribute Feld muss mit eines dieser Werte enden: :values.',
    'enum' => 'Das Gewählte :attribute ist ungültig.',
    'exists' => 'Das Gewählte :attribute ist ungültig.',
    'extensions' => 'Das :attribute Feld muss eines dieser Endungen haben: :values.',
    'file' => 'Das :attribute Feld muss eine Datei sein.',
    'filled' => 'Das :attribute Feld muss einen Wert haben.',
    'gt' => [
        'array' => 'Das :attribute Feld muss mehr als :value Einträge haben.',
        'file' => 'Das :attribute Feld muss größer als :value Kilobytes sein.',
        'numeric' => 'Das :attribute Feld muss größer als :value sein.',
        'string' => 'Das :attribute Feld muss mehr als :value Zeichen enthalten.',
    ],
    'gte' => [
        'array' => 'Das :attribute Feld muss :value oder mehr Einträge haben.',
        'file' => 'Das :attribute Feld muss :value Kilobytes oder größer sein.',
        'numeric' => 'Das :attribute Feld muss :value oder größer sein.',
        'string' => 'Das :attribute Feld muss :value oder mehr Zeichen enthalten.',
    ],
    'hex_color' => 'Das :attribute Feld muss eine gültige Hexadezimalfarbe sein.',
    'image' => 'Das :attribute Feld muss ein Bild sein.',
    'in' => 'Das Gewählte :attribute ist ungültig.',
    'in_array' => 'Das :attribute Feld muss in :other existieren.',
    'integer' => 'Das :attribute Feld muss eine Ganzzahl sein.',
    'ip' => 'Das :attribute Feld muss eine gültige IP-Adresse sein.',
    'ipv4' => 'Das :attribute Feld muss eine gültige IPv4-Adresse sein.',
    'ipv6' => 'Das :attribute Feld muss eine gültige IPv6-Adresse sein.',
    'json' => 'Das :attribute Feld muss eine gültige JSON-Zeichenfolge sein.',
    'list' => 'Das :attribute Feld muss eine Liste sein.',
    'lowercase' => 'Das :attribute Feld muss in Kleinbuchstaben sein.',
    'lt' => [
        'array' => 'Das :attribute Feld muss weniger als :value Einträge haben.',
        'file' => 'Das :attribute Feld muss weniger als :value Kilobytes sein.',
        'numeric' => 'Das :attribute Feld muss weniger als :value sein.',
        'string' => 'Das :attribute Feld muss weniger als :value Zeichen enthalten.',
    ],
    'lte' => [
        'array' => 'Das :attribute Feld darf nicht mehr als :value Einträge haben.',
        'file' => 'Das :attribute Feld muss :value Kilobytes oder weniger sein.',
        'numeric' => 'Das :attribute Feld muss :value oder weniger sein.',
        'string' => 'Das :attribute Feld muss :value oder weniger Zeichen enthalten.',
    ],
    'mac_address' => 'Das :attribute Feld muss eine gültige MAC-Adresse sein.',
    'max' => [
        'array' => 'Das :attribute Feld darf nicht mehr als :max Einträge haben.',
        'file' => 'Das :attribute Feld darf nicht mehr als :max Kilobytes sein.',
        'numeric' => 'Das :attribute Feld darf nicht größer als :max sein.',
        'string' => 'Das :attribute Feld darf nicht mehr als :max Zeichen enthalten.',
    ],
    'max_digits' => 'Das :attribute Feld darf nicht mehr als :max Ziffern enthalten.',
    'mimes' => 'Das :attribute Feld muss eine Datei mit eines dieser Typen sein: :values.',
    'mimetypes' => 'Das :attribute Feld muss eine Datei mit eines dieser Typen sein: :values.',
    'min' => [
        'array' => 'Das :attribute Feld muss mindestens :min Einträge haben.',
        'file' => 'Das :attribute Feld muss mindestens :min Kilobytes sein.',
        'numeric' => 'Das :attribute Feld muss mindestens :min sein.',
        'string' => 'Das :attribute Feld muss mindestens :min Zeichen enthalten.',
    ],
    'min_digits' => 'Das :attribute Feld muss mindestens :min Ziffern haben.',
    'missing' => 'Das :attribute Feld muss fehlen.',
    'missing_if' => 'Das :attribute Feld muss fehlen, wenn :other den Wert :value hat.',
    'missing_unless' => 'Das :attribute Feld muss fehlen, wenn :other nicht den Wert :value hat.',
    'missing_with' => 'Das :attribute Feld muss fehlen, wenn die Werte :values vorhanden sind.',
    'missing_with_all' => 'Das :attribute Feld muss fehlen, wenn die Werte :values vorhanden sind.',
    'multiple_of' => 'Das :attribute Feld muss ein Vielfaches von :value sein.',
    'not_in' => 'Das Ausgewählte :attribute ist ungültig.',
    'not_regex' => 'Das Format des :attribute Felds ist ungültig.',
    'numeric' => 'Das :attribute Feld muss eine Zahl sein.',
    'password' => [
        'letters' => 'Das :attribute Feld muss mindestens einen Buchstaben enthalten.',
        'mixed' => 'Das :attribute Feld muss mindestens einen Groß- und Kleinbuchstaben enthalten.',
        'numbers' => 'Das :attribute Feld muss mindestens eine Zahl enthalten.',
        'symbols' => 'Das :attribute Feld muss mindestens ein Sonderzeichen enthalten.',
        'uncompromised' => 'Die Werte des :attribute Felds kamen in einem Datenleck vor. Bitte wählen Sie etwas anderes.',
    ],
    'present' => 'Das :attribute Feld muss vorhanden sein.',
    'present_if' => 'Das :attribute Feld muss vorhanden sein, wenn :other den Wert :value hat.',
    'present_unless' => 'Das :attribute Feld muss vorhanden sein, wenn :other nicht den Wert :value hat.',
    'present_with' => 'Das :attribute Feld muss vorhanden sein, wenn die Werte :values vorhanden sind.',
    'present_with_all' => 'Das :attribute Feld muss vorhanden sein, wenn die Werte :values vorhanden sind.',
    'prohibited' => 'Das :attribute Feld ist verboten.',
    'prohibited_if' => 'Das :attribute Feld ist verboten, wenn :other den Wert :value hat.',
    'prohibited_unless' => 'Das :attribute Feld ist verboten, wenn :other nicht in :values ist.',
    'prohibits' => 'Das :attribute Feld verbietet die Existenz von :other. ',
    'regex' => 'Das Format von :attribute ist ungültig.',
    'required' => 'Das :attribute Feld ist ein Pflichtfeld.',
    'required_array_keys' => 'Das :attribute Feld muss Einträge enthalten für: :values.',
    'required_if' => 'Das :attribute Feld ist pflicht, wenn :other den Wert :value hat.',
    'required_if_accepted' => 'Das :attribute Feld ist pflicht, wenn :other akzeptiert ist.',
    'required_if_declined' => 'Das :attribute Feld ist pflicht, wenn :other abgelehnt ist.',
    'required_unless' => 'Das :attribute Feld ist pflicht, wenn :other nicht in :values ist.',
    'required_with' => 'Das :attribute Feld ist pflicht, wenn :values vorhanden ist.',
    'required_with_all' => 'Das :attribute Feld ist pflicht, wenn :values vorhanden sind.',
    'required_without' => 'Das :attribute Feld ist pflicht, wenn :values nicht vorhanden ist.',
    'required_without_all' => 'Das :attribute Feld ist pflicht, wenn keines aus :values vorhanden ist.',
    'same' => 'Das :attribute Feld muss dem :other Feld gleichen.',
    'size' => [
        'array' => 'Das :attribute Feld muss :size Einträge enthalten.',
        'file' => 'Das :attribute Feld muss :size Kilobytes sein.',
        'numeric' => 'Das :attribute Feld muss :size sein.',
        'string' => 'Das :attribute Feld muss :size Zeichen enthalten.',
    ],
    'starts_with' => 'Das :attribute Feld muss mit eines der Folgenden beginnen: :values.',
    'string' => 'Das :attribute Feld muss eine Zeichenfolge sein.',
    'timezone' => 'Das :attribute Feld muss eine gültige Zeitzone sein.',
    'unique' => ':attribute wurde bereits registriert.',
    'uploaded' => ':attribute konnte nicht hochgeladen werden.',
    'uppercase' => ':attribute muss in Großbuchstaben sein.',
    'url' => 'Das :attribute Feld muss eine gültige URL sein.',
    'ulid' => 'Das :attribute Feld muss eine gültige ULID sein.',
    'uuid' => 'Das :attribute Feld muss eine gültige UUID sein.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
            
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'email' => 'E-Mail Adresse',
        'firstname' => 'Vorname',
        'lastname' => 'Nachname',
        'password' => 'Passwort'
    ],

];
