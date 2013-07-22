<?php

defined('MOODLE_INTERNAL') || die();

class qtype_code_edit_form extends question_edit_form {

    protected function definition_inner($mform) {
        $menu = array(
            get_string('caseno', 'qtype_code'),
            get_string('caseyes', 'qtype_code')
        );
        $mform->addElement('select', 'usecase',
                get_string('casesensitive', 'qtype_code'), $menu);

	///sabir: adicionando textarea no enunciado
        ///$mform->addElement('textarea', 'mytextarea',"Sabir");

        $mform->addElement('static', 'answersinstruct',
                get_string('correctanswers', 'qtype_code'),
                get_string('filloutoneanswer', 'qtype_code'));
        $mform->closeHeaderBefore('answersinstruct');

        $this->add_per_answer_fields($mform, get_string('answerno', 'qtype_code', '{no}'),
                question_bank::fraction_options());

        $this->add_interactive_settings();

	///sabir: adicionando textarea no fim do enunciado
        ///$mform->addElement('textarea', 'mytextarea',"Sabir");
    }

    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_answers($question);
        $question = $this->data_preprocessing_hints($question);

        return $question;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $answers = $data['answer'];
        $answercount = 0;
        $maxgrade = false;
        foreach ($answers as $key => $answer) {
            $trimmedanswer = trim($answer);
            if ($trimmedanswer !== '') {
                $answercount++;
                if ($data['fraction'][$key] == 1) {
                    $maxgrade = true;
                }
            } else if ($data['fraction'][$key] != 0 ||
                    !html_is_blank($data['feedback'][$key]['text'])) {
                $errors["answer[$key]"] = get_string('answermustbegiven', 'qtype_code');
                $answercount++;
            }
        }
        if ($answercount==0) {
            $errors['answer[0]'] = get_string('notenoughanswers', 'qtype_code', 1);
        }
        if ($maxgrade == false) {
            $errors['fraction[0]'] = get_string('fractionsnomax', 'question');
        }
        return $errors;
    }

    public function qtype() {
        return 'code';
    }
}
