<?php

require_once 'Digirent/Form/Element.php';

/**
 * HTML form elements 
 *
 * SYNOPSIS:
 * <code>
 * require_once 'Digirent/Form/Elements.php';
 * $form = new Digirent_Form_Elements();
 *
 * $form->createElement('id');
 * $form->createElement('username');
 * $form->createElement('userpasswd');
 * $form->createElement('comment');
 *
 * // Create an element with (select|radio|checkbox) options.
 *
 * // The value of the "options" key is an associative array of values and output.
 * $form->createElement('gender',
 *                       array('options' => array('1' => 'male',
 *                                                '2' => 'female')));
 *
 * // The value of the "values" key is an array of values and outputs.
 * $form->createElement('favorite',
 *                       array('values' => array('Food', 'Travel', 'Shopping')));
 *
 * // The value of the "values" key is an array of values.
 * // The value of the "outputs" key is an array of outputs.
 * $form->createElement('vote',
 *                       array('values'  => array('1', '2', '3'),
 *                       array('outputs' => array('Good', 'Average', 'Bad'));
 *
 * $form->setValue('id', 1);
 * echo $form->toHTMLHidden('id'); // <input type="hidden" name="id" value="1" />
 *
 * echo $form->toHTMLText('username', 'size="20" maxlength="40"');
 * // <input type="text" name="username" size="20" maxlength="40" />
 *
 * // The HTML radios.
 * echo $form->setValue('gender', 2);
 * echo $form->toHTMLRadios('gender');
 * // <label><input type="radio" name="gender" value="1" />male</label>
 * // <label><input type="radio" name="gender" value="2" checked="checked" />female</label>
 *
 * // The HTML radio of each element.
 * echo $form->toHTMLRadio('gender', '1', 'id="gender1"');
 * // <input type="radio" name="gender" value="1" id="gender1" />
 * echo $form->toHTMLRadio('gender', '2', 'id="gender2"');
 * // <input type="radio" name="gender" value="2" id="gender2" checked="checked" />
 *
 * // The HTML select with options.
 * echo $form->toHTMLSelect('gender');
 * // <select name="gender">
 * // <option value="1">male</option>
 * // <option value="2">female</option>
 * // </select>
 *
 * // The HTML options only.
 * echo $form->toHTMLOptions('gender');
 * // <option value="1">male</option>
 * // <option value="2">female</option>
 *
 * // The multiple HTML checkboxes.
 * $form->setValue('favorite', array('Food', 'Shopping'));
 * echo $form->toHTMLCheckboxes('favorite[]');
 * // <label><input type="checkbox" name="favorite[]" value="Food" checked="checked" />Food</label>
 * // <label><input type="checkbox" name="favorite[]" value="Travel" />Travel</label>
 * // <label><input type="checkbox" name="favorite[]" value="Shopping" checked="checked" />Shopping</label>
 *
 * // The string with the ' / ' separator.
 * echo $form->toString('favorite', ' / ');
 * // "Food / Shopping"
 *
 * // Inject the values of the elements by an associative array.
 * $form->injectParams($_POST);
 * echo $form->toHTMLHiddens();
 * // <input type="hidden" name="id" value="...." />
 * // <input type="hidden" name="username" value="...." />
 * // <input type="hidden" name="userpasswd" value="...." />
 * // ....
 *
 * // The associative array of elements.
 * $params = $form->toArray();
 *
 * // Reset all values.
 * $form->clear();
 *
 * // It's serializable.
 * $serialized = $form->serialize();
 * $form->unserialize($serialized);
 * </code>
 *
 * @package  Digirent_Form
 */
class Digirent_Form_Elements
{
    /**
     * @access private
     * @var    array
     */
    var $elements = array();

    /**
     * @access public
     */
    function Digirent_Form_Elements()
    {
    }

    /**
     * @access public
     * @return string
     */
    function serialize()
    {
        return serialize($this->toArray());
    }

    /**
     * @access public
     * @param  string
     */
    function unserialize($serialized)
    {
        if ($serialized != '') {
            $params = (array) @unserialize($serialized);
            $this->injectParams($params);
        }
    }

    /**
     * @access public
     * @param  string
     * @param  array
     * @return array
     */
    function & createElement($name, $params = array())
    {
        $this->elements[$name] = new Digirent_Form_Element();

        $value = @$params['value'];
        if (!is_array($value)) {
            $value = (string) $value;
        }
        $this->elements[$name]->setValue($value);

        if (isset($params['options'])) {
            $this->elements[$name]->setOptions($params['options']);
        } else {
            if (isset($params['values'])) {
                if (isset($params['outputs'])) {
                    $this->elements[$name]->setOptions($params['values'], $params['outputs']);
                } else {
                    $this->elements[$name]->setOptions($params['values'], $params['values']);
                }
            }
        }

        return $this->elements[$name];
    }

    /**
     * @access public
     * @param  string
     * @return object
     */
    function & getElement($name)
    {
        $element = null;
        if (isset($this->elements[$name])) {
            $element =& $this->elements[$name];
        }
        return $element;
    }

    /**
     * @access public
     * @param  string
     * @param  array
     */
    function setElement($name, &$element)
    {
        $this->elements[$name] =& $element;
    }

    /**
     * @access public
     * @param  string
     */
    function removeElement($name)
    {
        unset($this->elements[$name]);
    }

    /**
     * @access public
     */
    function removeElements()
    {
        $this->elements = array();
    }

    /**
     * @access public
     * @param  string
     * @return array
     */
    function getOptions($name)
    {
        $options = null;
        if ($element =& $this->getElement($name)) {
            $options = $element->getOptions();
        }
        return $options;
    }

    /**
     * @access public
     * @param  string
     * @param  array
     */
    function setOptions($name, $values, $outputs = null)
    {
        if ($element =& $this->getElement($name)) {
            $element->setOptions($values, $outputs);
        }
    }

    /**
     * @access public
     * @param  string
     * @return mixed
     */
    function getValue($name)
    {
        $value = null;
        if ($element =& $this->getElement($name)) {
            $value = $element->getValue();
        }

        return $value;
    }

    /**
     * @access public
     * @param  string
     * @param  mixed
     */
    function setValue($name, $value)
    {
        if ($element =& $this->getElement($name)) {
            $element->setValue($value);
        }
    }

    /**
     * @access public
     * @param  array
     */
    function injectParams($params)
    {
        foreach ($params as $key => $value) {
            $this->setValue($key, $value);
        }
    }

    /**
     * @access public
     * @param  string
     * @param  string
     * @param  string
     * @return string
     */
    function toHTMLInput($type, $name, $attributes = '')
    {
        if (preg_match('/^(.+)\[\]$/', $name, $matches)) {
            $name     = $matches[1];
            $multiple = true;
        } else {
            $multiple = false;
        }

        $html = '';
        if (!$element =& $this->getElement($name)) {
            return $html;
        }

        if ((string) $attributes !== '') {
            $attributes = ' ' . preg_replace('/^ */', '', $attributes);
        }
        $values = (array) $element->getValue();
        foreach ($values as $value) {
            $html .= sprintf('<input type="%s" name="%s" value="%s"%s />',
                             $type, $name . ($multiple ? '[]' : ''),
                             htmlspecialchars($value), $attributes);
        }

        return $html;
    }

    /**
     * @access public
     * @param  string
     * @param  string
     * @return string
     */
    function toHTMLText($name, $attributes = '')
    {
        return $this->toHTMLInput('text', $name, $attributes);
    }

    /**
     * @access public
     * @param  string
     * @param  string
     * @return string
     */
    function toHTMLPassword($name, $attributes = '')
    {
        return $this->toHTMLInput('password', $name, $attributes);
    }

    /**
     * @access public
     * @param  string
     * @param  string
     * @return string
     */
    function toHTMLHidden($name, $attributes = '')
    {
        return $this->toHTMLInput('hidden', $name, $attributes);
    }

    /**
     * @access public
     * @param  string
     * @return string
     */
    function toHTMLHiddens($attributes = '')
    {
        $html = '';
        $names = array_keys($this->elements);
        foreach ($names as $name) {
            $multiple = is_array($this->getValue($name));
            $html .= $this->toHTMLHidden($name . ($multiple ? '[]' : ''), $attributes);
        }
        return $html;
    }

    /**
     * @access public
     * @param  string
     * @param  string
     * @return string
     */
    function toHTMLTextarea($name, $attributes = '')
    {
        if (preg_match('/^(.+)\[\]$/', $name, $matches)) {
            $name     = $matches[1];
            $multiple = true;
        } else {
            $multiple = false;
        }

        $html = '';
        if (!$element =& $this->getElement($name)) {
            return $html;
        }

        if ((string) $attributes !== '') {
            $attributes = ' ' . preg_replace('/^ */', '', $attributes);
        }

        $values = (array) $element->getValue();
        foreach ($values as $value) {
            $html = sprintf('<textarea name="%s"%s>',
                            $name . ($multiple ? '[]' : ''), $attributes);
            $html .= htmlspecialchars($value);
            $html .= '</textarea>';
        }

        return $html;
    }

    /**
     * @access public
     * @param  string
     * @param  string
     * @return string
     */
    function toHTMLSelect($name, $attributes = '')
    {
        if (preg_match('/^(.+)\[\]$/', $name, $matches)) {
            $name     = $matches[1];
            $multiple = true;
        } else {
            $multiple = false;
        }

        $html = '';
        if (!$element =& $this->getElement($name)) {
            return $html;
        }

        if ((string) $attributes !== '') {
            $attributes = ' ' . preg_replace('/^ */', '', $attributes);
        }
        $html = sprintf('<select name="%s%s"%s>', $name, ($multiple ? '[]' : ''), $attributes) . "\n";
        $html .= $this->toHTMLOptions($name);
        $html .= '</select>';

        return $html;
    }

    /**
     * @access public
     * @param  string
     * @return string
     */
    function toHTMLOptions($name)
    {
        $html = '';

        if (!$element =& $this->getElement($name)) {
            return $html;
        }

        $values  = (array) $element->getValue();
        $options = (array) $element->getOptions();
        foreach ($options as $value => $output) {
            if ((string) $output === '') {
                $output = (string) $value;
            }
            $selected = in_array((string) $value, $values)
                      ? ' selected="selected"' : '';
            $html .= sprintf('<option value="%s"%s>%s</option>',
                             htmlspecialchars($value), $selected,
                             htmlspecialchars($output)) . "\n";
        }

        return $html;
    }

    /**
     * @access public
     * @param  string
     * @param  string
     * @param  string
     * @return string
     */
    function toHTMLOption($name, $value, $output = null)
    {
        $html = '';

        if (!$element =& $this->getElement($name)) {
            return $html;
        }

        if ($output === null) {
            $output = htmlspecialchars($value);
        }
        $value = (string) $value;
        $selected = in_array((string) $value, (array) $element->getValue()) ? ' selected="selected"' : '';
        $html = sprintf('<option value="%s"%s>%s</option>',
                        htmlspecialchars($value), $selected, $output);


        return $html;
    }

    /**
     * @access public
     * @param  string
     * @param  string
     * @param  string
     * @return string
     */
    function toHTMLRadio($name, $value, $attributes = '')
    {
        $html = '';

        if (!$element =& $this->getElement($name)) {
            return $html;
        }

        $value = (string) $value;
        if ((string) $attributes !== '') {
            $attributes = ' ' . preg_replace('/^ */', '', $attributes);
        }

        $checked = in_array((string) $value, (array) $element->getValue())
                 ? ' checked="checked"' : '';
        $html = sprintf('<input type="radio" name="%s" value="%s"%s%s />',
                        $name, htmlspecialchars($value), $attributes, $checked);

        return $html;
    }

    /**
     * @access public
     * @param  string
     * @param  string
     * @param  string
     * @param  string
     * @return string
     */
    function toHTMLRadios($name, $separator = '&nbsp;', $format = '%s', $attributes = '')
    {
        $html = '';

        if (!$element =& $this->getElement($name)) {
            return $html;
        }

        $options = array();
        if ((string) $attributes !== '') {
            $attributes = ' ' . preg_replace('/^ */', '', $attributes);
        }
        foreach ($element->getOptions() as $value => $output) {
            $checked = in_array((string) $value, (array) $element->getValue())
                     ? ' checked="checked"' : '';
            $options[] = sprintf('<label><input type="radio" name="%s" value="%s"%s%s />' . $format . '</label>',
                                 $name, htmlspecialchars($value), $attributes,
                                 $checked, htmlspecialchars($output));
        }
        $html = implode($separator, $options);

        return $html;
    }

    /**
     * @access public
     * @param  string
     * @param  string
     * @param  string
     * @return string
     */
    function toHTMLCheckbox($name, $value, $attributes = '')
    {
        if (preg_match('/^(.+)\[\]$/', $name, $matches)) {
            $name     = $matches[1];
            $multiple = true;
        } else {
            $multiple = false;
        }

        $html = '';
        if (!$element =& $this->getElement($name)) {
            return $html;
        }

        $value = (string) $value;
        if ((string) $attributes !== '') {
            $attributes = ' ' . preg_replace('/^ */', '', $attributes);
        }
        $checked = in_array((string) $value, (array) $element->getValue())
                 ? ' checked="checked"' : '';
        $html = sprintf('<input type="checkbox" name="%s%s" value="%s"%s%s />',
                        $name, ($multiple ? '[]' : ''),
                        htmlspecialchars($value), $attributes, $checked);

        return $html;
    }

    /**
     * @access public
     * @param  string
     * @param  string
     * @param  string
     * @param  string
     * @return string
     */
    function toHTMLCheckboxes($name, $separator = '&nbsp;', $format = '%s', $attributes = '')
    {
        if (preg_match('/^(.+)\[\]$/', $name, $matches)) {
            $name     = $matches[1];
            $multiple = true;
        } else {
            $multiple = false;
        }

        $html = '';
        if (!$element =& $this->getElement($name)) {
            return $html;
        }

        $options = array();
        if ((string) $attributes !== '') {
            $attributes = ' ' . preg_replace('/^ */', '', $attributes);
        }
        foreach ($element->getOptions() as $value => $output) {
            $checked = in_array((string) $value, (array) $element->getValue())
                     ? ' checked="checked"' : '';
            $options[] = sprintf('<label><input type="checkbox" name="%s%s" value="%s"%s%s />' . $format . '</label>',
                                 $name, ($multiple ? '[]' : ''),
                                 htmlspecialchars($value), $attributes,
                                 $checked, htmlspecialchars($output));
        }
        $html = implode($separator, $options);

        return $html;
    }

    /**
     * @access public
     * @param  string
     * @param  string
     * @param  string
     * @return string
     */
    function toString($name, $format = '%s', $separator = ' ')
    {
        $string = '';
        if (!$element =& $this->getElement($name)) {
            return $string;
        }

        if ($options = $element->getOptions()) {
            $values  = (array) $element->getValue();
            $outputs = array();
            foreach ($values as $value) {
                $value = (string) $value;
                if (isset($options[$value])) {
                    $outputs[] = sprintf($format, $options[$value]);
                }
                $string = implode($separator, $outputs);
            }
        } else {
            $string = (string) sprintf($format, $element->getValue());
        }

        return $string;
    }

    /**
     * @access public
     * @return array
     */
    function toArray()
    {
        $params = array();
        $names = array_keys($this->elements);
        foreach ($names as $name) {
            $element =& $this->getElement($name);
            $params[$name] = $element->getValue();
        }
        return $params;
    }

    /**
     * @access public
     */
    function clear()
    {
        $names = array_keys($this->elements);
        foreach ($names as $name) {
            $element =& $this->getElement($name);
            $element->setValue('');
        }
    }

    /**
     * @access public
     * @param  string
     * @param  string
     * @return array
     */
    function & splitValues($name, $separator = ' ')
    {
        $values = array();

        if ($element =& $this->getElement($name)) {
            $lines = explode($separator, $element->getValue());
            foreach ($lines as $line) {
                $line = (string) trim($line);
                if ($line !== '') { $values[] = $line; }
            }
        }

        return $values;
    }

    /**
     * @access public
     * @param  array
     * @param  string
     * @return string
     */
    function & joinValues($names, $separator = '')
    {
        $values = array();
        foreach ((array) $names as $name) {
            if ($element =& $this->getElement($name)) {
                $values[] = $element->getValue();
            }
        }

        $value = implode($separator, $values);

        return $value;
    }
}

