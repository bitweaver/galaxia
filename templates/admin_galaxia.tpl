{strip}
{form}
	{jstabs}
		{jstab title="Galaxia Settings"}
			{legend legend="Galaxia Settings"}
				<input type="hidden" name="page" value="{$page}" />

				{foreach from=$formGalaxia key=item item=output}
					<div class="control-group">
						{formlabel label=$output.label for=$item}
						{forminput}
							{html_checkboxes name="$item" values="y" checked=$gBitSystem->getConfig($item) labels=false id=$item}
							{formhelp note=$output.note page=$output.page}
						{/forminput}
					</div>
				{/foreach}

				<div class="control-group submit">
					<input type="submit" class="btn btn-default" name="list_submit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}
	{/jstabs}
{/form}

{/strip}
