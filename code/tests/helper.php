<?php

defined('MOODLE_INTERNAL') || die();


/**
 * Test helper class for the code question type.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_code_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('frogtoad', 'frogonly', 'escapedwildcards');
    }

    /**
     * Makes a code question with correct ansewer 'frog', partially
     * correct answer 'toad' and defaultmark 1. This question also has a
     * '*' match anything answer.
     * @return qtype_code_question
     */
    public function make_code_question_frogtoad() {
        question_bank::load_question_definition_classes('code');
        $sa = new qtype_code_question();
        test_question_maker::initialise_a_question($sa);
        $sa->name = 'Code question';
        $sa->questiontext = 'Name an amphibian: __________';
        $sa->generalfeedback = 'Generalfeedback: frog or toad would have been OK.';
        $sa->usecase = false;
        $sa->answers = array(
            13 => new question_answer(13, 'frog', 1.0, 'Frog is a very good answer.', FORMAT_HTML),
            14 => new question_answer(14, 'toad', 0.8, 'Toad is an OK good answer.', FORMAT_HTML),
            15 => new question_answer(15, '*', 0.0, 'That is a bad answer.', FORMAT_HTML),
        );
        $sa->qtype = question_bank::get_qtype('code');

        return $sa;
    }

    /**
     * Gets the question data for a code question with with correct
     * ansewer 'frog', partially correct answer 'toad' and defaultmark 1.
     * This question also has a '*' match anything answer.
     * @return stdClass
     */
    public function get_code_question_data_frogtoad() {
        $qdata = new stdClass();
        test_question_maker::initialise_question_data($qdata);

        $qdata->qtype = 'code';
        $qdata->name = 'Code question';
        $qdata->questiontext = 'Name an amphibian: __________';
        $qdata->generalfeedback = 'Generalfeedback: frog or toad would have been OK.';

        $qdata->options = new stdClass();
        $qdata->options->usecase = false;
        $qdata->options->answers = array(
            13 => new question_answer(13, 'frog', 1.0, 'Frog is a very good answer.', FORMAT_HTML),
            14 => new question_answer(14, 'toad', 0.8, 'Toad is an OK good answer.', FORMAT_HTML),
            15 => new question_answer(15, '*', 0.0, 'That is a bad answer.', FORMAT_HTML),
        );

        return $qdata;
    }

    /**
     * Makes a code question with just the correct ansewer 'frog', and
     * no other answer matching.
     * @return qtype_code_question
     */
    public function make_code_question_frogonly() {
        question_bank::load_question_definition_classes('code');
        $sa = new qtype_code_question();
        test_question_maker::initialise_a_question($sa);
        $sa->name = 'Code question';
        $sa->questiontext = 'Name the best amphibian: __________';
        $sa->generalfeedback = 'Generalfeedback: you should have said frog.';
        $sa->usecase = false;
        $sa->answers = array(
            13 => new question_answer(13, 'frog', 1.0, 'Frog is right.', FORMAT_HTML),
        );
        $sa->qtype = question_bank::get_qtype('code');

        return $sa;
    }

    /**
     * Gets the question data for a code questionwith just the correct
     * ansewer 'frog', and no other answer matching.
     * @return stdClass
     */
    public function get_code_question_data_frogonly() {
        $qdata = new stdClass();
        test_question_maker::initialise_question_data($qdata);

        $qdata->qtype = 'code';
        $qdata->name = 'Code question';
        $qdata->questiontext = 'Name the best amphibian: __________';
        $qdata->generalfeedback = 'Generalfeedback: you should have said frog.';

        $qdata->options = new stdClass();
        $qdata->options->usecase = false;
        $qdata->options->answers = array(
            13 => new question_answer(13, 'frog', 1.0, 'Frog is right.', FORMAT_HTML),
        );

        return $qdata;
    }

    /**
     * Makes a code question with just the correct ansewer 'frog', and
     * no other answer matching.
     * @return qtype_code_question
     */
    public function make_code_question_escapedwildcards() {
        question_bank::load_question_definition_classes('code');
        $sa = new qtype_code_question();
        test_question_maker::initialise_a_question($sa);
        $sa->name = 'Question with escaped * in the answer.';
        $sa->questiontext = 'How to you write x times y in C? __________';
        $sa->generalfeedback = 'In C, this expression is written x * y.';
        $sa->usecase = false;
        $sa->answers = array(
            13 => new question_answer(13, '*x\*y*', 1.0, 'Well done.', FORMAT_HTML),
        );
        $sa->qtype = question_bank::get_qtype('code');

        return $sa;
    }
}
