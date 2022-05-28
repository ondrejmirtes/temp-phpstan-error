<?php
/**
 * Some view/html/shortcuts
 * to create form elements with their wrapers
 * 
 * @author itaymoav
 */
class ViewSnipets_F_E{
	const	FIELD_SIZE_XL	= 'xl',
			FIELD_SIZE_LG	= 'lg',
			FIELD_SIZE_SM	= 'sm'
	;
	
	static public function static_field($label,$value){?>
			<div class="form-group">
              <label class="control-label"><?=$label?></label>
              <div class="form-field form-field-static"><p><?=$value?></p></div>
            </div>	
	
<?	}

	static public function hidden_field(Form_Element_Simple $element){
?>
			 <input type="text" class="form-control hidden" name="<?=$element->name()?>" id="<?= $element->name()?>"  value="<?=$element->v()?>" />
<?
	}
	
	static public function password(Form_Element_Simple $element,$label=''){
	    return self::password_v($element->name(),$label);
	}
	
	static public function password_v($name,$label=''){
	    return self::input($name,'password',$label,'');
	}
	
	static public function text(Form_Element_Simple $element,$label=''){
		return self::text_v($element->name(),$label,$element->v());
	}
	
	static public function text_v($name,$label='',$value=''){
        return self::input($name,'text',$label,$value);
	}
    
    static private function input($name,$type,$label='',$value='',$input_attributes = []){
        $label=$label?:ucwords(str_replace('_',' ',$name));
        $label=str_replace(['[',']'],['<p class="help-block">','</p>'],$label);
        ?>
	            <div class="form-group">
					<label class="control-label" for="<?=$name?>"><?=$label?></label>
					<input type="<?=$type?>" name="<?=$name?>" id="<?=$name?>" <?foreach($input_attributes as $attribute => $attribute_value):?> <?= $attribute?>="<?= $attribute_value?>" <? endforeach;?>class="form-control width-jumbo" value="<?=$value?>">
					<span class="help-block <?=$name?>_msg has-error"></span>
				</div>
        <?
    }

	static public function textarea(Form_Element_Simple $element, $label='', $required=true, $size=self::FIELD_SIZE_LG){
		$class_required=$required?' required':'';
?>
				<div class="form-group <?= $class_required?>">
				<label class="control-label"><?= $label?></label>
				<div class="form-field">
					<textarea type="text" class="form-control" rows="3" name="<?=$element->name()?>" id="<?= $element->name()?>" value="<?=$element->v()?>"><?=$element->v()?></textarea>
					<span class="help-block <?= $element->name()?>_msg"></span>
				</div>
			</div>
<? 
	}
}
