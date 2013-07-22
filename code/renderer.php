<?php

defined('MOODLE_INTERNAL') || die();

    ///sabir:
    function get_lang_from_last_line($code)
    {
        $lines = explode("\n", $code);
        $lastline = $lines[count($lines)-1];
        $c = array('//cpp' => 'cpp','//sce' => 'sce', '#py' => 'py');
        $lang = $c[$lastline];
        if (!$lang) $lang = 'cpp';
        //die($lang);
        return $lang;
    }

class qtype_code_renderer extends qtype_renderer {
    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {

        $question = $qa->get_question();
        $currentanswer = $qa->get_last_qt_var('answer');

        $inputname = $qa->get_qt_field_name('answer');
        $inputattributes = array(
            ///sabir: Tipo da resposta
            ///como usar type = textarea? Checkbox funcionou!
            ///'type' => 'checkbox',
            'name' => $inputname,
            'id' => $inputname,

            //'type' => 'text',
            //'name' => $inputname,
            //'value' => $currentanswer,
            //'id' => $inputname,
            //'size' => 80,
        );

	$disabled = "";
        if ($options->readonly) {
            $inputattributes['readonly'] = 'readonly';
	    $disabled = "disabled";
        }

        $feedbackimg = '';
        if ($options->correctness) {
            $answer = $question->get_matching_answer(array('answer' => $currentanswer));
            if ($answer) {
                $fraction = $answer->fraction;
            } else {
                $fraction = 0;
            }
            $inputattributes['class'] = $this->feedback_class($fraction);
            $feedbackimg = $this->feedback_image($fraction);
        }

        $questiontext = $question->format_questiontext($qa);
        $placeholder = false;
        if (preg_match('/_____+/', $questiontext, $matches)) {
            $placeholder = $matches[0];
            $inputattributes['size'] = round(strlen($placeholder) * 1.1);
        }
        $input = html_writer::empty_tag('input', $inputattributes) . $feedbackimg;

	///sabir: adicionando select lang
	//$input.= html_writer::empty_tag('input', array('type'=>'radio',) );
	$lang = get_lang_from_last_line($currentanswer);
	$input = "<br/><select name='".$inputname."_inputlang' id='".$inputname."_inputlang' $disabled >".
                "<option value='cpp'>C++</option>".
		"<option value='py'>Python</option>".
                "<option value='sce'>Scilab</option>".
		"</select><br/>";

	$input.="<script>document.getElementById('".$inputname."_inputlang').value='".$lang."';</script>";

	//$lang = get_lang_from_last_line($currentanswer);

        ///sabir: é assim que substitui input por textarea
        $input.= html_writer::empty_tag('textarea', $inputattributes).$currentanswer.'</textarea>' . $feedbackimg;

	//$input.= '<script>onsubmit=function () { var code = document.getElementById("'.$inputname.'").value; var lang = document.getElementById("'.$inputname.'_inputlang").value; var c = "//"; if (lang == "py") c = "#"; var newcode = code + "\n\n" + c + lang ; document.getElementById("'.$inputname.'").value = newcode; return true;} //document.getElementById("'.$inputname.'_inputlang").value="'.$lang.'"; </script>';

	$input.= '<script>if(typeof funcs === "undefined") var funcs=new Array; funcs.push( function(){ var code = document.getElementById("'.$inputname.'").value; var lang = document.getElementById("'.$inputname.'_inputlang").value; var c = "//"; if (lang == "py") c = "#"; var newcode = code + "\n\n" + c + lang ; document.getElementById("'.$inputname.'").value = newcode; } ); onsubmit=function(){ for (var i = 0; i < funcs.length; i++) funcs[i](); return true;}</script>';

        if ($placeholder) {
            $inputinplace = html_writer::tag('label', get_string('answer'),
                    array('for' => $inputattributes['id'], 'class' => 'accesshide'));
            $inputinplace .= $input;
            $questiontext = substr_replace($questiontext, $inputinplace,
                    strpos($questiontext, $placeholder), strlen($placeholder));
        }

        $result = html_writer::tag('div', $questiontext, array('class' => 'qtext'));

        if (!$placeholder) {
            $result .= html_writer::start_tag('div', array('class' => 'ablock'));
            $result .= html_writer::tag('label', get_string('answer', 'qtype_code',
                    html_writer::tag('span', $input, array('class' => 'answer'))),
                    array('for' => $inputattributes['id']));
            $result .= html_writer::end_tag('div');
        }

        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div',
                    $question->get_validation_error(array('answer' => $currentanswer)),
                    array('class' => 'validationerror'));
        }

        return $result;
    }

    public function specific_feedback(question_attempt $qa) {
        $question = $qa->get_question();

        $answer = $question->get_matching_answer(array('answer' => $qa->get_last_qt_var('answer')));
        if (!$answer || !$answer->feedback) {
            return '';
        }

        return $question->format_text($answer->feedback, $answer->feedbackformat,
                $qa, 'question', 'answerfeedback', $answer->id);
    }

    public function correct_response(question_attempt $qa) {
        $question = $qa->get_question();

        $answer = $question->get_matching_answer($question->get_correct_response());
        if (!$answer) {
            return '';
        }

        return get_string('correctansweris', 'qtype_code',
                s($question->clean_response($answer->answer)));
    }
}
