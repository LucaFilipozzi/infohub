<style type="text/css">
	table.swagger {
		width: 100%;
	}
	table.swagger td input {
		width: 100%;
	}
</style>
<div class="innerLower">
	<?= $this->Form->create() ?>
		<?= $this->Form->input('host') ?>
		<?= $this->Form->input('basePath') ?>
		<table class="swagger">
			<tr>
				<th>Field</th>
				<th width="1%">Business Term</th>
				<th width="1%">Context</th>
			</tr>
			<?php foreach ($this->request->data['Swagger']['elements'] as $index => $element): ?>
				<tr>
					<td><?= $this->Form->input("Swagger.elements.{$index}.name", ['label' => false, 'class' => 'data-label', 'data-index' => $index]) ?></td>
					<td>
						<?= $this->Form->input("Swagger.elements.{$index}.business_term", ['type' => 'hidden', 'id' => "origTerm{$index}"]) ?>
						<?= $this->Form->input("Swagger.elements.{$index}.business_term", ['label' => false, 'class' => 'bt-select', 'data-index' => $index, 'type' => 'select']) ?>
					</td>
					<td class="view-context<?= $index ?>" style="white-space: nowrap"></td>
					<td class="xview-definition<?= $index ?>"></td>
				</tr>
			<?php endforeach ?>
		</table>
		<?= $this->Form->submit() ?>
	<?= $this->Form->end() ?>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		var loadingStatus = {};
		var lookupCache = {};
		var idCache = {};

		$('.data-label').change(function() {
			var $this = $(this);
			var full = $this.val();
			var period = full.lastIndexOf('.');
			var label = full.substring(period + 1);
			$this.data('label', label);
			setOptions($this);
		}).change();

		$('.bt-select').change(function() {
			var $this = $(this);
			var selected = $this.val();
			var index = $this.data('index');
			$('.temp-view' + index).html('');
			if (!selected) {
				return;
			}
			if (idCache[selected] === undefined) {
				return;
			}
			$('.view-context' + index).html(idCache[selected].context);
			$('.view-definition' + index).html(idCache[selected].definition);
		});

		function setOptions($name) {
			var index = $name.data('index');
			var label = $name.data('label');
			var $select = $('#SwaggerElements' + index + 'BusinessTerm');

			$select.html('');

			if (label == '') {
				return;
			}

			if (lookupCache[label] === undefined) {
				$select.html('<option value="">Loading...<option>');
				loadLabel(label);
				return;
			}

			var origTerm = $('#origTerm' + index).val();
			$select.append($('<option>', {value: '', text: ''}));
			var matched = false;
			for (var i in lookupCache[label]) {
				var option = lookupCache[label][i]
				var attributes = {value: option.id, text: option.name, title: option.title};
				if (option.id == origTerm) {
					matched = true;
					attributes.selected = 'selected';
				}
				$select.append($('<option>', attributes));
			}
			if (!matched && lookupCache[label].length > 0) {
				$select.val(lookupCache[label][0].id); //default select first option
			}
			$select.change();
		}

		function loadLabel(label) {
			if (loadingStatus[label] !== undefined) {
				return;
			}
			loadingStatus[label] = true;
			$.post('<?= $this->Html->url(['action' => 'find_business_term']) ?>', {label: label}, function(data) {
				delete loadingStatus[label];
				if (!data instanceof Array) {
					return;
				}
				lookupCache[label] = [];
				for (var i in data) {
					var title = '';
					var context = '';
					var definition = '';
					if (data[i].context !== undefined && data[i].context.val) {
						context = data[i].context.val
						title = context + ' - ';
					}
					if (data[i].definition !== undefined) {
						definition = data[i].definition.val
						title += definition;
					}
					lookupCache[label][i] = {
						id: data[i].name.id,
						name: data[i].name.val,
						title: title,
						context: context,
						definition: definition
					};
					idCache[data[i].name.id] = lookupCache[label][i];
				}
				$('.data-label')
					.filter(function () {
						return $(this).data('label') == label;
					}).each(function() {
						setOptions($(this));
					});
			});
		}
	})
</script>