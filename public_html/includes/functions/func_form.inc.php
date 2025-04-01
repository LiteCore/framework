<?php

	function form_begin($name='', $method='post', $action=false, $multipart=false, $parameters='') {
		return '<form'. (($name) ? ' name="'. functions::escape_attr($name) .'"' : '') .' method="'. ((strtolower($method) == 'get') ? 'get' : 'post') .'" enctype="'. (($multipart == true) ? 'multipart/form-data' : 'application/x-www-form-urlencoded') .'" accept-charset="'. mb_http_output() .'"'. (($action) ? ' action="'. functions::escape_attr($action) .'"' : '') . ($parameters ? ' ' . $parameters : '') .'>';
	}

	function form_end() {
		return '</form>';
	}

	function form_reinsert_value($name) {

		if (!$name) return;

		foreach ([$_POST, $_GET] as $superglobal) {

			if (!$superglobal) continue;

			// Extract name parts
			$parts = preg_split('#[\]\[]+#', preg_replace('#\[\]$#', '', $name), -1, PREG_SPLIT_NO_EMPTY);

			// Get array node
			$node = $superglobal;

			foreach ($parts as $part) {
				if (!isset($node[$part])) continue 2;
				$node = $node[$part];
			}

			return $node;
		}

		return preg_match('#\[\]$#', $name) ? [] : '';
	}

	function form_parameters($parameters) {

		if (is_array($parameters)) {
			$parameters = implode(' ', array_map(function($attribute, $value) {
				return $attribute .'="'. functions::escape_attr($value) .'"';
			}, array_keys($parameters), $parameters));
		}

		return $parameters;
	}

	function form_button($name, $value, $type='submit', $parameters='', $fonticon='') {

		if (!is_array($value)) {
			$value = [$value, $value];
		}

		$parameters = form_parameters($parameters);

		return '<button'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="btn btn-default"' : '') .' type="'. functions::escape_attr($type) .'" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($value[0]) .'"'. ($parameters ? ' '. $parameters : '') .'>'. ($fonticon ? functions::draw_fonticon($fonticon) . ' ' : '') . (isset($value[1]) ? $value[1] : $value[0]) .'</button>';
	}

	function form_button_link($url, $title, $parameters='', $fonticon='') {
		return '<a '. (!preg_match('#class="([^"]+)?"#', $parameters) ? 'class="btn btn-default"' : '') .' href="'. functions::escape_attr($url) .'"'. ($parameters ? ' '. $parameters : '') .'>'. ($fonticon ? functions::draw_fonticon($fonticon) . ' ' : '') . $title .'</a>';
	}

	function form_button_predefined($name, $parameters='') {

		switch ($name) {

			case 'enable':
				return functions::form_button('enable', language::translate('title_enable', 'Enable'), 'submit', $parameters, 'on');

			case 'disable':
				return functions::form_button('disable', language::translate('title_disable', 'Disable'), 'submit', $parameters, 'off');

			case 'save':
				return functions::form_button('save', language::translate('title_save', 'Save'), 'submit', 'class="btn btn-success"' . ($parameters ? ' '. $parameters : ''), 'save');

			case 'delete':
				return functions::form_button('delete', language::translate('title_delete', 'Delete'), 'submit', 'formnovalidate class="btn btn-danger" onclick="if (!confirm(&quot;'. language::translate('text_are_you_sure', 'Are you sure?') .'&quot;)) return false;"' . ($parameters ? ' '. $parameters : ''), 'delete');

			case 'cancel':
				return functions::form_button('cancel', language::translate('title_cancel', 'Cancel'), 'button', 'onclick="history.go(-1);"' . ($parameters ? ' '. $parameters : ''), 'cancel');
		}

		trigger_error('Unknown predefined button ('. functions::escape_html($name) .')', E_USER_WARNING);

		return form_button($name, $value, 'submit', $parameters);
	}

	function form_dropdown($name, $options=[], $input=true, $parameters='') {

		$html = implode(PHP_EOL, [
			'<div class="dropdown"'. ($parameters ? ' ' . $parameters : '') .'>',
			'	<div class="form-select" data-toggle="dropdown">-- '. language::translate('title_select', 'Select') .' --</div>',
			'	<ul class="dropdown-menu">',
		]);

		$is_numerical_index = array_is_list($options);

		foreach ($options as $key => $option) {

			if (!is_array($option)) {
				if ($is_numerical_index) {
					$option = [$option, $option];
				} else {
					$option = [$key, $option];
				}
			}

			if (preg_match('#\[\]$#', $name)) {
				$html .= '<li class="dropdown-item">' . functions::form_checkbox($name, $option, $input, isset($option[2]) ? $option[2] : '') .'</li>' . PHP_EOL;
			} else {
				$html .= '<li class="dropdown-item">' . functions::form_radio_button($name, $option, $input, isset($option[2]) ? $option[2] : '') .'</li>' . PHP_EOL;
			}
		}

		$html .= '	</ul>' . PHP_EOL
				 . '</div>';

		return $html;
	}

	function form_checkbox($name, $value, $input=true, $parameters='') {

		if (is_array($value)) {
			return implode(PHP_EOL, [
				'<label'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-check"' : '') .'>',
				'	' . form_checkbox($name, $value[0], $input, $parameters),
				'	' . (isset($value[1]) ? $value[1] : $value[0]),
				'</label>',
			]);
		}

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		if (preg_match('#\[\]$#', $name)) {
			return '<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-check"' : '') .' type="checkbox" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($value) .'" '. ((is_array($input) && in_array($value, $input)) ? ' checked' : '') . ($parameters ? ' ' . $parameters : '') .'>';
		} else {
			return '<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-check"' : '') .' type="checkbox" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($value) .'" '. (!strcmp($input, $value) ? ' checked' : '') . ($parameters ? ' ' . $parameters : '') .'>';
		}
	}

	function form_captcha($id, $config=[], $parameters='') {

		$config = [
			'width' => !empty($config['width']) ? $config['width'] : 100,
			'height' => !empty($config['height']) ? $config['height'] : 40,
			'length' => !empty($config['length']) ? $config['length'] : 4,
			'set' => !empty($config['set']) ? $config['set'] : 'numbers',
		];

		return functions::captcha_draw($id, $config, $parameters);
	}

	function form_input_code($name, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		document::$javascript[] = implode(PHP_EOL, [
			'$(\'textarea[name="'. $name .'"]\').on(\'keydown\', function(e) {',
			'	if (e.keyCode != 9) return;',
			'	e.preventDefault();',
			' var start = this.selectionStart, end = this.selectionEnd;',
			'	this.value = this.value.substring(0, start) + \'\t\' + this.value.substring(end);',
			'	this.selectionStart = this.selectionEnd = start + 1;',
			'});',
		]);

		return '<textarea'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-code"' : '') .' name="'. functions::escape_attr($name) .'"'. ($parameters ? ' '. $parameters : '') .'>'. functions::escape_html($input) .'</textarea>';
	}

	function form_input_color($name, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		return '<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-input"' : '') .' type="color" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($input) .'"'. ($parameters ? ' '. $parameters : '') .'>';
	}

	function form_input_csv($name, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		if (!$csv = functions::csv_decode($input)) {
			return form_textarea($name, $input, $parameters);
		}

		$html = [
			'<table class="table data-table" data-toggle="csv">',
			'	<thead>',
			'		<tr>'
		];

		foreach (array_keys($csv[0]) as $column) {
			$html += ['			<th>'. $column .'</th>'];
		}

		$html += [
			'			<th><a class="add-column" href="#">'. functions::draw_fonticon('icon-plus', 'style="color: #6c6;"') .'</a></th>',
			'		</tr>',
			'	</thead>',
			'	<tbody>',
		];

		foreach ($csv as $row) {
			$html += ['		<tr>'];
			foreach ($row as $columns) {
				$html += ['			<td contenteditable>'. $row[$column] .'</td>'];
			}
			$html += [
				'			<td><a class="btn btn-default btn-sm remove" href="#">'. functions::draw_fonticon('icon-times', 'style="color: #d33"') .'</a></td>',
				'		</tr>',
			];
		}

		$html += [
			'	</tbody>',
			'	<tfoot>',
			'		<tr>',
			'			<td colspan="'. (count($columns)+1) .'"><a class="add-row" href="#">'. functions::draw_fonticon('icon-plus', 'style="color: #6c6;"') .'</a></td>',
			'		</tr>',
			'	</tfoot>',
			'</table>',
			'',
			form_textarea($name, $input, 'style="display: none;"'),
		];

		$html = implode(PHP_EOL, $html);

		document::$javascript['table2csv'] = implode(PHP_EOL, [
			"$('table[data-toggle=\"csv\"]').on('click', '.remove', function(e) {",
			"	e.preventDefault();",
			"	var parent = $(this).closest('tbody');",
			"	$(this).closest('tr').remove();",
			"	$(parent).trigger('keyup');",
			"});",
			"",
			"$('table[data-toggle=\"csv\"] .add-row').click(function(e) {",
			"	e.preventDefault();",
			"	var n = $(this).closest('table').find('thead th:not(:last-child)').length;",
			"	$(this).closest('table').find('tbody').append(",
			"		'<tr>' + ('<td contenteditable></td>'.repeat(n)) + '<td><a class=\"remove\" href=\"#\"><i class=\"icon-times-circle\" style=\"color: #d33;\"></i></a></td>' +'</tr>'",
			"	).trigger('keyup');",
			"});",
			"",
			"$('table[data-toggle=\"csv\"] .add-column').click(function(e) {",
			"	e.preventDefault();",
			"	var table = $(this).closest('table');",
			"	var title = prompt(\"<?php echo language::translate('title_column_title', 'Column Title'); ?>\");",
			"	if (!title) return;",
			"	$(table).find('thead tr th:last-child:last-child').before('<th>'+ title +'</th>');",
			"	$(table).find('tbody tr td:last-child:last-child').before('<td contenteditable></td>');",
			"	$(table).find('tfoot tr td').attr('colspan', $(this).closest('table').find('tfoot tr td').attr('colspan') + 1);",
			"	$(this).trigger('keyup');",
			"});",
			"",
			"$('table[data-toggle=\"csv\"]').keyup(function(e) {",
			"	 var csv = $(this).find('thead tr, tbody tr').map(function (i, row) {",
			"			return $(row).find('th:not(:last-child),td:not(:last-child)').map(function (j, col) {",
			"				var text = \$(col).text();",
			"				if (/('|,)/.test(text)) {",
			"					return '\"'+ text.replace(/\"/g, '\"\"') +'\"';",
			"				} else {",
			"					return text;",
			"				}",
			"			}).get().join(',');",
			"		}).get().join('\\r\\n');',
			'	$(this).next('textarea').val(csv);",
			"});",
		]);

		return $html;
	}

	function form_input_date($name, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		if ($input && !in_array(substr($input, 0, 10), ['0000-00-00', '1970-01-01'])) {
			$input = date('Y-m-d', strtotime($input));
		} else {
			$input = '';
		}

		return '<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-input"' : '') .' type="date" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($input) .'" placeholder="YYYY-MM-DD"'. ($parameters ? ' '. $parameters : '') .'>';
	}

	function form_input_datetime($name, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		if ($input && !in_array(substr($input, 0, 10), ['0000-00-00', '1970-01-01'])) {
			$input = date('Y-m-d\TH:i', strtotime($input));
		} else {
			$input = '';
		}

		return '<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-input"' : '') .' type="datetime-local" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($input) .'" placeholder="YYYY-MM-DD [hh:nn]"'. ($parameters ? ' '. $parameters : '') .'>';
	}

	function form_input_decimal($name, $input=true, $decimals=null, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		if ($input != '' && is_numeric($decimals)) {

			// Circumvent floating point precision problem if differing by one 10th of the smallest fraction

			$fractions = strpos($input, '.') ? strlen(substr(strrchr($input, '.'), 1)) : 0;
			$absdiff = abs((float)$input - round((float)$input, 2));
			$offset = (1 / pow(10, $decimals+1));

			if ($fractions < $decimals) {
				$input = number_format((float)$input, $decimals, '.', '');
			} else if ($absdiff > $offset) {
				$input = number_format((float)$input, $decimals+2, '.', '');
			} else {
				$input = number_format((float)$input, $decimals, '.', '');
			}
		}

		return '<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-input"' : '') .' type="number" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($input) .'" step="any" data-decimals="'. (int)$decimals .'"'. ($parameters ? ' '. $parameters : '') .'>';
	}

	function form_input_email($name, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		return implode(PHP_EOL, [
			'<div class="input-group">',
			'	<span class="input-group-icon">'. functions::draw_fonticon('icon-envelope') .'</span>',
			'	<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-input"' : '') .' type="email" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($input) .'"'. ($parameters ? ' '. $parameters : '') .'>',
			'</div>',
		]);
	}

	function form_input_file($name, $parameters='') {
		return '<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-input"' : '') .' type="file" name="'. functions::escape_attr($name) .'"'. ($parameters ? ' '. $parameters : '') .'>';
	}

	function form_input_hidden($name, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		return '<input type="hidden" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($input) .'"'. ($parameters ? ' '. $parameters : '') .'>';
	}

	function form_input_month($name, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		if (!in_array(substr($input, 0, 7), ['', '0000-00', '1970-00', '1970-01'])) {
			$input = date('Y-m', strtotime($input));
		} else {
			$input = '';
		}

		return '<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-input"' : '') .' type="month" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($input) .'" maxlength="7" pattern="[0-9]{4}-[0-9]{2}" placeholder="YYYY-MM"'. ($parameters ? ' '. $parameters : '') .'>';
	}

	function form_input_money($name, $currency_code=null, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		if (!$currency_code) {
			$currency_code = settings::get('site_currency_code');
		}

		$currency = currency::$currencies[$currency_code];

		if ($input != '') {
			$input = number_format((float)$input, $currency['decimals'], '.', '');
			//$input = rtrim(preg_replace('#(\.'. str_repeat('\d', 2) .')0{1,2}$#', '$1', $input), '.'); // Auto decimals
		}

		$parameters = ($parameters ? $parameters .' ' : '') . 'placeholder="'. language::number_format(0, $currency['decimals']) .'"';

		return implode(PHP_EOL, [
			'<div class="input-group">',
			'	<strong class="input-group-text" style="opacity: 0.75; font-family: monospace;">'. functions::escape_html($currency['code']) .'</strong>',
			'	' . form_input_decimal($name, $input, $currency['decimals'], ($parameters ? $parameters .' ' : '') .'step="any" data-type="currency"'),
			'</div>',
		]);
	}

	function form_input_number($name, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		if ($input != '') {
			$input = round((int)$input);
		}

		return '<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-input"' : '') .' type="number" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($input) .'" step="1"'. ($parameters ? ' '. $parameters : '') .'>';
	}

	function form_input_password($name, $input='', $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		return implode(PHP_EOL, [
			'<div class="input-group">',
			'	<span class="input-group-icon">'. functions::draw_fonticon('icon-key') .'</span>',
			'	<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-input"' : '') .' type="password" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($input) .'"'. ($parameters ? ' '. $parameters : '') .'>',
			'</div>',
		]);
	}

	function form_input_password_unmaskable($name, $input='', $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		return implode(PHP_EOL, [
			'<div class="input-group">',
			'	<span class="input-group-icon">'. functions::draw_fonticon('icon-key') .'</span>',
			'	<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-input"' : '') .' type="password" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($input) .'"'. ($parameters ? ' '. $parameters : '') .'>',
			'	<button class="btn btn-default" type="button" onclick="$(this).prev().attr(\'type\', ($(this).prev().attr(\'type\') == \'password\') ? \'text\' : \'password\')">'. functions::draw_fonticon('icon-eye') .'</button>',
			'</div>',
		]);
	}

	function form_input_percent($name, $input=true, $decimals=2, $parameters='') {

		return implode(PHP_EOL, [
			'<div class="input-group">',
			'	' . form_input_decimal($name, $input, $decimals, $parameters),
			'	<span class="input-group-text">%</span>',
			'</div>',
		]);
	}

	function form_input_phone($name, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		return implode(PHP_EOL, [
			'<div class="input-group">',
			'	<span class="input-group-icon">'. functions::draw_fonticon('icon-phone') .'</span>',
			'	<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-input"' : '') .' type="tel" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($input) .'" pattern="\+?([0-9]|-| )+"'. ($parameters ? ' '. $parameters : '') .'>',
			'</div>',
		]);
	}

	function form_radio_button($name, $value, $input=true, $parameters='') {

		if (is_array($value)) {
			return implode(PHP_EOL, [
				'<label'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-check"' : '') .'>',
				'	' . form_radio_button($name, $value[0], $input, $parameters),
				'	' . (isset($value[1]) ? $value[1] : $value[0]),
				'</label>',
			]);
		}

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		return '<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-radio"' : '') .' type="radio" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($value) .'" '. (!strcmp($input, $value) ? ' checked' : '') . ($parameters ? ' ' . $parameters : '') .'>';
	}

	function form_input_range($name, $input=true, $min='', $max='', $step='', $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		return '<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-range"' : '') .' type="range" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($input) .'" min="'. (float)$min .'" max="'. (float)$max .'" step="'. (float)$step .'"'. ($parameters ? ' '. $parameters : '') .'>';
	}

	function form_input_search($name, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		return implode(PHP_EOL, [
			'<div class="input-group">',
			'	<span class="input-group-icon">'. functions::draw_fonticon('icon-search') .'</span>',
			'	<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-input"' : '') .' type="search" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($input) .'"'. ($parameters ? ' '. $parameters : '') .'>',
			'</div>',
		]);
	}

	function form_input_tags($name, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		return '<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-input"' : '') .' type="text" data-toggle="tags" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($input) .'"'. ($parameters ? ' '. $parameters : '') .'>';
	}

	function form_input_text($name, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		return '<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-input"' : '') .' type="text" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($input) .'"'. ($parameters ? ' '. $parameters : '') .'>';
	}

	function form_input_time($name, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		return '<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-input"' : '') .' type="time" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($input) .'"'. ($parameters ? ' '. $parameters : '') .'>';
	}

	function form_input_url($name, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		return '<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-input"' : '') .' type="url" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($input) .'"'. ($parameters ? ' '. $parameters : '') .'>';
	}

	function form_input_username($name, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		return implode(PHP_EOL, [
			'<div class="input-group">',
			'	<span class="input-group-icon">'. functions::draw_fonticon('icon-user') .'</span>',
			'	<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-input"' : '') .' type="text" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($input) .'"'. ($parameters ? ' '. $parameters : '') .'>',
			'</div>',
		]);
	}

	function form_input_week($name, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		if ($input && !in_array(substr($input, 0, 10), ['0000-00-00', '1970-01-01'])) {
			$input = date('Y-\WW', strtotime($input));
		} else {
			$input = '';
		}

		return '<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-input"' : '') .' type="week" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($input) .'" maxlength="7" pattern="[0-9]{4}-W[0-9]{2}" placeholder="YYYY-WW"'. ($parameters ? ' '. $parameters : '') .'>';
	}

	function form_input_wysiwyg($name, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		document::load_style([
			'app://assets/trumbowyg/ui/trumbowyg.min.css',
			'app://assets/trumbowyg/plugins/colors/ui/trumbowyg.colors.min.css',
			'app://assets/trumbowyg/plugins/table/ui/trumbowyg.table.min.css',
		], 'trumbowyg');

		document::load_script([
			'app://assets/trumbowyg/trumbowyg.min.js',
			'app://assets/trumbowyg/plugins/colors/trumbowyg.colors.min.js',
			'app://assets/trumbowyg/plugins/upload/trumbowyg.upload.min.js',
			'app://assets/trumbowyg/plugins/table/trumbowyg.table.min.js',
		], 'trumbowyg');

		if (language::$selected['code'] != 'en') {
			document::load_script('app://assets/trumbowyg/langs/'. language::$selected['code'] .'.min.js', 'trumbowyg-lang');
		}

		document::$javascript[] = implode(PHP_EOL, [
			'$(\'textarea[name="'. $name .'"]\').trumbowyg({',
			'	btns: [["viewHTML"], ["formatting"], ["strong", "em", "underline", "del"], ["foreColor", "backColor"], ["link"], ["insertImage"], ["table"], ["justifyLeft", "justifyCenter", "justifyRight"], ["lists"], ["preformatted"], ["horizontalRule"], ["removeformat"], ["fullscreen"]],',
			'	btnsDef: {',
			'		lists: {',
			'			dropdown: ["unorderedList", "orderedList"],',
			'			title: "Lists",',
			'			ico: "unorderedList",',
			'		}',
			'	},',
			'	plugins: {',
			'		upload: {',
			'			serverPath: "'. document::href_rlink('app://assets/trumbowyg/plugins/upload/trumbowyg.upload.php') .'",',
			'		}',
			'	},',
			'	lang: "'. language::$selected['code'] .'",',
			'	autogrowOnEnter: true,',
			'	imageWidthModalEdit: true,',
			'	removeformatPasted: true,',
			'	semantic: false',
			'});',
		]);

		return '<textarea name="'. functions::escape_attr($name) .'"'. ($parameters ? ' '. $parameters : '') .'>'. functions::escape_html($input) .'</textarea>';
	}

	function form_regional($name, $language_code='', $input=true, $type='text', $parameters='') {

		if (!$language_code) {
			$language_code = settings::get('site_language_code');
		}

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		return implode(PHP_EOL, [
			'<div class="input-group">',
			'	<span class="input-group-text" style="font-family: monospace;" title="'. functions::escape_attr(language::$languages[$language_code]['name']) .'">'. functions::escape_html($language_code) .'</span>',
			'	<input class="form-input" name="'. functions::escape_attr($name) .'" type="'. functions::escape_attr($type) .'" value="'. functions::escape_attr($input) .'">',
			'</div>'
		]);
	}

	function form_regional_text($name, $language_code='', $input=true, $parameters='') {

		if (!$language_code) {
			$language_code = settings::get('site_language_code');
		}

		return implode(PHP_EOL, [
			'<div class="input-group">',
			'	<span class="input-group-text" style="font-family: monospace;" title="'. functions::escape_attr(language::$languages[$language_code]['name']) .'">'. functions::escape_html($language_code) .'</span>',
			'	' . form_input_text($name, $input, $parameters),
			'</div>',
		]);
	}

	function form_regional_textarea($name, $language_code='', $input=true, $parameters='') {

		if (!$language_code) {
			$language_code = settings::get('site_language_code');
		}

		return implode(PHP_EOL, [
			'<div class="input-group">',
			'	<span class="input-group-text" style="font-family: monospace;" title="'. functions::escape_attr(language::$languages[$language_code]['name']) .'">'. functions::escape_html($language_code) .'</span>',
			'	' . form_textarea($name, $input, $parameters),
			'</div>',
		]);
	}

	function form_regional_wysiwyg($name, $language_code='', $input=true, $parameters='') {

		if (!$language_code) {
			$language_code = settings::get('site_language_code');
		}

		return implode(PHP_EOL, [
			'<div class="input-group">',
			'	<span class="input-group-text" style="font-family: monospace;" title="'. functions::escape_attr(language::$languages[$language_code]['name']) .'">'. functions::escape_html($language_code) .'</span>',
			'	' . form_input_wysiwyg($name, $input, $parameters),
			'</div>',
		]);
	}

	function form_select($name, $options=[], $input=true, $parameters='') {

		if (preg_match('#\[\]$#', $name)) {
			return form_select_multiple($name, $options, $input, $parameters);
		}

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		$html = '<select '. (!preg_match('#class="([^"]+)?"#', $parameters) ? 'class="form-select"' : '') .' name="'. functions::escape_attr($name) .'"'. ($parameters ? ' ' . $parameters : '') .'>' . PHP_EOL;

		$is_numerical_index = array_is_list($options);

		foreach ($options as $key => $option) {

			if (!is_array($option)) {
				if ($is_numerical_index) {
					$option = [$option];
				} else {
					$option = [$key, $option];
				}
			}

			$html .= '	<option value="'. functions::escape_attr($option[0]) .'"'. (!strcmp($option[0], $input) ? ' selected' : '') . ((isset($option[2])) ? ' ' . $option[2] : '') . '>'. (isset($option[1]) ? $option[1] : $option[0]) .'</option>' . PHP_EOL;
		}

		$html .= '</select>';

		return $html;
	}

	function form_select_multiple($name, $options=[], $input=true, $parameters='') {

		$html = '<div class="form-input"' . ($parameters ? ' ' . $parameters : '') .'>' . PHP_EOL;

		$is_numerical_index = array_is_list($options);

		foreach ($options as $key => $option) {

			if (!is_array($option)) {
				if ($is_numerical_index) {
					$option = [$option, $option];
				} else {
					$option = [$key, $option];
				}
			}

			$html .= form_checkbox($name, $option, $input, isset($option[2]) ? $option[2] : '');
		}

		$html .= '</div>';

		return $html;
	}

	function form_select_optgroup($name, $groups=[], $input=true, $parameters='') {

		if (!is_array($groups)) {
			$groups = [$groups];
		}

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		$html = '<select class="form-select" name="'. functions::escape_attr($name) .'"'. (preg_match('#\[\]$#', $name) ? ' multiple' : '') . ($parameters ? ' ' . $parameters : '') .'>' . PHP_EOL;

		foreach ($groups as $group) {
			$html .= '	<optgroup label="'. functions::escape_attr($group['label']) .'">' . PHP_EOL;

			$is_numerical_index = array_is_list($group['options']);

			foreach ($group['options'] as $key => $option) {

				if (!is_array($option)) {
					if ($is_numerical_index) {
						$option = [$option, $option];
					} else {
						$option = [$key, $option];
					}
				}

				if (preg_match('#\[\]$#', $name)) {
					$html .= '	<option value="'. functions::escape_attr($option[0]) .'"'. (in_array($option[0], $input) ? ' selected' : '') . (!empty($option[2]) ? ' ' . $option[2] : '') . '>'. (isset($option[1]) ? $option[1] : $option[0]) .'</option>' . PHP_EOL;
				} else {
					$html .= '	<option value="'. functions::escape_attr($option[0]) .'"'. (!strcmp($option[0], $input) ? ' selected' : '') . (!empty($option[2]) ? ' ' . $option[2] : '') . '>'. (isset($option[1]) ? $option[1] : $option[0]) .'</option>' . PHP_EOL;
				}
			}

			$html .= '	</optgroup>' . PHP_EOL;
		}

		$html .= '</select>';

		return $html;
	}

	function form_switch($name, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		return implode(PHP_EOL, [
			'<div class="form-switch"'. ($parameters ? ' '. $parameters : '') .'>',
			'	<label><input type="checkbox" name="'. functions::escape_attr($name) .'" value="1" hidden'. ($input ? ' checked' : '') .'></label>',
			'</div>',
		]);
	}

	function form_textarea($name, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		return '<textarea'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-input"' : '') .' name="'. functions::escape_attr($name) .'"'. ($parameters ? ' '. $parameters : '') .'>'. functions::escape_html($input) .'</textarea>';
	}

	function form_toggle($name, $options='t/f', $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		if (is_string($options)) {
			switch ($options) {

				case 'a/i':
					$options = [
						'1' => language::translate('title_active', 'Active'),
						'0' => language::translate('title_inactive', 'Inactive'),
					];
					break;

				case 'e/d':
					$options = [
						'1' => language::translate('title_enabled', 'Enabled'),
						'0' => language::translate('title_disabled', 'Disabled'),
					];
					break;

				case 'y/n':
					$options = [
						'1' => language::translate('title_yes', 'Yes'),
						'0' => language::translate('title_no', 'No'),
					];
					break;

				case 'o/o':
					$options = [
						'1' => language::translate('title_on', 'On'),
						'0' => language::translate('title_off', 'Off'),
					];
					break;

				case 't/f':
					$options = [
						'1' => language::translate('title_true', 'True'),
						'0' => language::translate('title_false', 'False'),
					];
					break;

				default:
					trigger_error('Invalid option ("'. $options.'")', E_USER_WARNING);
					$options = [
						'1' => language::translate('title_true', 'True'),
						'0' => language::translate('title_false', 'False'),
					];
					break;
			}
		}

		$html = '<div '. (!preg_match('#class="([^"]+)?"#', $parameters) ? 'class="form-toggle"' : '') .''. ($parameters ? ' '. $parameters : '') .'>'. PHP_EOL;

		$is_numerical_index = (is_array($options) && array_is_list($options)) ? true : false;

		foreach ($options as $key => $option) {

			if (!is_array($option)) {
				if ($is_numerical_index) {
					$option = [$option, $option];
				} else {
					$option = [$key, $option];
				}
			}

			if (preg_match('#\[\]$#', $name)) {
				$html .= implode(PHP_EOL, [
					'	<label>',
					'		<input type="checkbox" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($option[0]) .'" hidden'. ((is_array($input) && in_array($option[0], $input)) ? ' checked' : '') . (!empty($option[2]) ? ' '. $option[2] : '') .'>'. $option[1],
					'	</label>',
				]) . PHP_EOL;
			} else {
				$html .= implode(PHP_EOL, [
					'	<label>',
					'		<input type="radio" name="'. functions::escape_attr($name) .'" value="'. functions::escape_attr($option[0]) .'" hidden'. (($option[0] == $input) ? ' checked' : '') . (!empty($option[2]) ? ' '. $option[2] : '') .'>'. $option[1],
					'	</label>',
				]) . PHP_EOL;
			}
		}

		$html .= '</div>';

		return $html;
	}

	##################################
	# Platform specific form helpers #
	##################################

	function form_function($name, $function, $input=true, $parameters='') {

		if (!preg_match('#(\w*)\((.*?)\)$#i', $function, $matches)) {
			trigger_error('Invalid form function ('. $function .')', E_USER_WARNING);
			return form_textarea($name, $input, $parameters);
		}

		$options = [];
		if (!empty($matches[2])) {
			$options = preg_split('#\s*,\s*#', $matches[2], -1, PREG_SPLIT_NO_EMPTY);
			$options = array_map(function($s){ return trim($s, '\'" '); }, $options);
		}

		switch ($matches[1]) {

			case 'administrator':
				return form_select_administrator($name, $input, $parameters);

			case 'bigtext':
				return form_textarea($name, $input, $parameters . ' rows="10"');

			case 'checkbox':
				return implode(PHP_EOL, array_map(function($option) use ($name, $input, $parameters) {
					return form_checkbox($name, [$option, $option], $input, $parameters);
				}, $options));

			case 'code':
				return form_input_code($name, $input, $parameters);

			case 'color':
				return form_input_color($name, $input, $parameters);

			case 'csv':
				return form_textarea($name, $input, true, $parameters);

			case 'date':
				return form_input_date($name, $input, $parameters);

			case 'datetime':
				return form_input_datetime($name, $input, $parameters);

			case 'decimal':
				return form_input_decimal($name, $input, 2, $parameters);

			case 'email':
				return form_input_email($name, $input, $parameters);

			case 'file':
				return form_select_file($name, $options[0], $input, $parameters);

			case 'language':
				return form_select_language($name, $input, $parameters);

			case 'number':
				return form_input_number($name, $input, $parameters);

			case 'password':
				return form_input_password($name, $input, $parameters);

			case 'password_unmaskable':
				return form_input_password_unmaskable($name, $input, $parameters);

			case 'percent':
				return form_input_percent($name, $input);

			case 'phone':
				return form_input_phone($name, $input);

			case 'radio':
				return implode(PHP_EOL, array_map(function($option) use ($name, $input, $parameters) {
					return form_radio_button($name, [$option, $option], $input, $parameters);
				}, $options));

			case 'regional_text':
				$html = '';
				foreach (array_keys(language::$languages) as $language_code) {
					$html .= form_regional_text($name.'['. $language_code.']', $language_code, $input, $parameters);
				}
				return $html;

			case 'regional_textarea':
				$html = '';
				foreach (array_keys(language::$languages) as $language_code) {
					$html .= form_regional_textarea($name.'['. $language_code.']', $language_code, $input, $parameters);
				}
				return $html;

			case 'regional_wysiwyg':
				$html = '';
				foreach (array_keys(language::$languages) as $language_code) {
					$html .= form_regional_wysiwyg($name.'['. $language_code.']', $language_code, $input, $parameters);
				}
				return $html;

			case 'select':
				for ($i=0; $i<count($options); $i++) $options[$i] = [$options[$i]];
				return form_select($name, $options, $input, $parameters);

			case 'tags':
				return form_tags($name, $input, $parameters);

			case 'text':
				return form_input_text($name, $input, $parameters);

			case 'textarea':
				return form_textarea($name, $input, $parameters);

			case 'time':
				return form_input_time($name, $input, $parameters);

			case 'timezone':
				return form_select_timezone($name, $input, $parameters);

			case 'toggle':
				return form_toggle($name, fallback($options[0], null), $input);

			case 'upload':
				return form_input_file($name, $parameters);

			case 'url':
				return form_input_url($name, $input, $parameters);

			case 'wysiwyg':
				return form_input_wysiwyg($input, $name, $parameters);

			case 'zone':
				$option = $options ? $options[0] : '';
				return form_select_zone($name, $option, $input, $parameters);

			default:
				trigger_error('Unknown function name "'. $function .'"', E_USER_WARNING);
				return form_input_text($name, $input, $parameters);
				break;
		}
	}

	function form_select_administrator($name, $input=true, $parameters='') {

		$options = database::query(
			"select id, username from ". DB_TABLE_PREFIX ."administrators
			order by username;"
		)->fetch_all(function($administrator){
			return [$administrator['id'], $administrator['username']];
		});

		if (preg_match('#\[\]$#', $name)) {
			return form_select_multiple($name, $options, $input, $parameters);
		} else {
			array_unshift($options, ['', '-- '. language::translate('title_select', 'Select') . ' --']);
			return form_select($name, $options, $input, $parameters);
		}
	}

	function form_select_encoding($name, $input=true, $parameters='') {

		$options = [
			'BIG-5',
			'CP50220',
			'CP50221',
			'CP50222',
			'CP51932',
			'CP850',
			'CP932',
			'EUC-CN',
			'EUC-JP',
			'EUC-KR',
			'EUC-TW',
			'GB18030',
			'ISO-8859-1',
			'ISO-8859-2',
			'ISO-8859-3',
			'ISO-8859-4',
			'ISO-8859-5',
			'ISO-8859-6',
			'ISO-8859-7',
			'ISO-8859-8',
			'ISO-8859-9',
			'ISO-8859-10',
			'ISO-8859-13',
			'ISO-8859-14',
			'ISO-8859-15',
			'ISO-8859-16',
			'KOI8-R',
			'KOI8-U',
			'SJIS',
			'UTF-8',
			'UTF-16',
			'Windows-1251',
			'Windows-1252',
			'Windows-1254',
		];

		if (preg_match('#\[\]$#', $name)) {
			return form_select_multiple($name, $options, $input, $parameters);
		} else {
			array_unshift($options, ['', '-- '. language::translate('title_select', 'Select') . ' --']);
			return form_select($name, $options, $input, $parameters);
		}
	}

	function form_select_function($name, $parameters='') {

		if (preg_match('#\[\]$#', $name)) {
			return form_select_multiple_files($name, $options, $input, $parameters);
		}

		$options = [
			'administrator()',
			'bigtext()',
			'checkbox()',
			'color()',
			'csv()',
			'currency()',
			'customer()',
			'date()',
			'datetime()',
			'decimal()',
			'email()',
			'file()',
			'geo_zone()',
			'language()',
			'mediumtext()',
			'number()',
			'password()',
			'percent()',
			'phone()',
			'radio()',
			'regional_text()',
			'regional_textarea()',
			'regional_wysiwyg()',
			'select()',
			'tags()',
			'text()',
			'textarea()',
			'time()',
			'timezone()',
			'toggle()',
			'upload()',
			'tax_class()',
			'url()',
			'wysiwyg()',
			'zone()',
		];

		if (preg_match('#\[\]$#', $name)) {
			return form_select_multiple($name, $options, $input, $parameters);
		} else {
			array_unshift($options, ['', '-- '. language::translate('title_select', 'Select') . ' --']);
			return form_select($name, $options, $input, $parameters);
		}
	}

	function form_select_file($name, $pattern, $input=true, $parameters='') {

		if (preg_match('#\[\]$#', $name)) {
			return form_select_multiple_files($name, $pattern, $input, $parameters);
		}

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		return implode(PHP_EOL, [
			'<div class="form-input"'. ($parameters ? ' ' . $parameters : '') .'>',
			'	' . form_input_hidden($name, true),
			'	<span class="value">'. ($input ? functions::escape_html($input) : '('. language::translate('title_none', 'None') .')') .'</span> <a href="'. document::href_ilink('b:files/file_picker') .'" data-toggle="lightbox" class="btn btn-default btn-sm" style="margin-inline-start: 5px;">'. language::translate('title_change', 'Change') .'</a>',
			'</div>',
		]);

		return '<input'. (!preg_match('#class="([^"]+)?"#', $parameters) ? ' class="form-input"' : '') .' type="file" name="'. functions::escape_attr($name) .'"'. ($parameters ? ' '. $parameters : '') .'>';
	}

	function form_select_multiple_files($name, $pattern, $input=true, $parameters='') {

		if (!preg_match('#\[\]$#', $name)) {
			return form_select_file($name, $options, $input, $parameters);
		}

		$options = array_map(function($file) {
			$file = preg_replace('#^'. preg_quote('app://', '#') .'#', '', $file);
			if (is_dir('app://' . $file)) {
				return [basename($file).'/', $file.'/'];
			} else {
				return [basename($file), $file];
			}
		}, functions::file_search($pattern, GLOB_BRACE));

		if (preg_match('#\[\]$#', $name)) {
			return form_select_multiple($name, $options, $input, $parameters);
		} else {
			array_unshift($options, ['', '-- '. language::translate('title_select', 'Select') . ' --']);
			return form_select($name, $options, $input, $parameters);
		}
	}

	function form_select_language($name, $input=true, $parameters='') {

		$options = array_map(function($language){
			return [$language['code'], $language['name'], 'data-decimal-point="'. $language['decimal_point'] .'" data-thousands-sep="'. $language['thousands_sep'] .'"'];
		}, language::$languages);

		if (preg_match('#\[\]$#', $name)) {
			return form_select_multiple($name, $options, $input, $parameters);
		} else {
			array_unshift($options, ['', '-- '. language::translate('title_select', 'Select') . ' --']);
			return form_select($name, $options, $input, $parameters);
		}
	}

	function form_select_intl_locale($name, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		if (!class_exists('ResourceBundle')) {
			trigger_error('The PHP extension "intl" is required to use form_select_locale()', E_USER_WARNING);
			return form_input_text($name, $input, $parameters . ($parameters ? ' ' : '') .'placeholder="en_US.utf8, en-US.UTF-8, english"');
		}

		$options = array_map(function($locale){
			return [$locale];
		}, ResourceBundle::getLocales(''));

		if (preg_match('#\[\]$#', $name)) {
			return form_select_multiple($name, $options, $input, $parameters);
		} else {
			array_unshift($options, ['', '-- '. language::translate('title_select', 'Select') . ' --']);
			return form_select($name, $options, $input, $parameters);
		}
	}

	function form_select_system_locale($name, $input=true, $parameters='') {

		if ($input === true) {
			$input = form_reinsert_value($name);
		}

		if (preg_match('#^WIN#i', PHP_OS)) {
			return form_input_text($name, $input, $parameters . ($parameters ? ' ' : '') .'placeholder="en-US,english"');
		}

		$options = array_map(function($locale){
			return [$locale];
		}, preg_split('#\R+#', shell_exec('locale -a'), -1, PREG_SPLIT_NO_EMPTY));

		if (preg_match('#\[\]$#', $name)) {
			return form_select_multiple($name, $options, $input, $parameters);
		} else {
			array_unshift($options, ['', '-- '. language::translate('title_select', 'Select') . ' --']);
			return form_select($name, $options, $input, $parameters);
		}
	}

	function form_select_month($name, $input=true, $parameters='') {

		$options = array_map(function($month){
			return [$month, functions::datetime_format('%B', date("Y-$month-1"))];
		}, range(1, 12));

		if (preg_match('#\[\]$#', $name)) {
			return form_select_multiple($name, $options, $input, $parameters);
		} else {
			array_unshift($options, ['', '-- '. language::translate('title_select', 'Select') . ' --']);
			return form_select($name, $options, $input, $parameters);
		}
	}

	function form_select_mysql_collation($name, $input=true, $parameters='') {

		$options = database::query(
			"select COLLATION_NAME from information_schema.COLLATIONS
			where CHARACTER_SET_NAME = 'utf8mb4'
			order by COLLATION_NAME;"
		)->fetch_all('COLLATION_NAME');

		if (preg_match('#\[\]$#', $name)) {
			return form_select_multiple($name, $options, $input, $parameters);
		} else {
			array_unshift($options, ['', '-- '. language::translate('title_select', 'Select') . ' --']);
			return form_select($name, $options, $input, $parameters);
		}
	}

	function form_select_mysql_engine($name, $input=true, $parameters='') {

		$options = database::query(
			"SHOW ENGINES;"
		)->fetch_all(function($engine){
			if (!in_array(strtoupper($engine['Support']), ['YES', 'DEFAULT'])) return false;
			if (!in_array($engine['Engine'], ['InnoDB', 'MyISAM', 'Aria'])) return false;
			return [$engine['Engine'], $engine['Engine'] . ' -- '. $engine['Comment']];
		});

		if (preg_match('#\[\]$#', $name)) {
			return form_select_multiple($name, $options, $input, $parameters);
		} else {
			array_unshift($options, ['', '-- '. language::translate('title_select', 'Select') . ' --']);
			return form_select($name, $options, $input, $parameters);
		}
	}

	function form_select_timezone($name, $input=true, $parameters='') {

		$options = array_filter(array_map(function($timezone){
			$timezone = explode('/', $timezone); // 0 => Continent, 1 => City

			if (empty($timezone[1]) || !in_array($timezone[0], ['Africa', 'America', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific'])) {
				return false;
			}

			return implode('/', $timezone);
		}, timezone_identifiers_list()));

		if (preg_match('#\[\]$#', $name)) {
			return form_select_multiple($name, $options, $input, $parameters);
		} else {
			array_unshift($options, ['', '-- '. language::translate('title_select', 'Select') . ' --']);
			return form_select($name, $options, $input, $parameters);
		}
	}
