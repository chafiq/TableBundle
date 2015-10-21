<?php

namespace EMC\TableBundle\Table\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EMC\TableBundle\Table\Column\ColumnInterface;

/**
 * Date Column
 *
 * @author Chafiq El Mechrafi <chafiq.elmechrafi@gmail.com>
 */
class DatetimeType extends ColumnType {

    const FORMAT_NONE = 'NONE';
    const FORMAT_FULL = 'FULL';
    const FORMAT_LONG = 'LONG';
    const FORMAT_MEDIUM = 'MEDIUM';
    const FORMAT_SHORT = 'SHORT';

    public static $formats = array(
        self::FORMAT_NONE => \IntlDateFormatter::NONE,
        self::FORMAT_FULL => \IntlDateFormatter::FULL,
        self::FORMAT_LONG => \IntlDateFormatter::LONG,
        self::FORMAT_MEDIUM => \IntlDateFormatter::MEDIUM,
        self::FORMAT_SHORT => \IntlDateFormatter::SHORT,
    );

    /**
     * {@inheritdoc}
     */
    public function buildView(array &$view, ColumnInterface $column, array $data, array $options) {
        parent::buildView($view, $column, $data, $options);
    }

    /**
     * {@inheritdoc}
     * <br/>
     * <br/>
     * Available Options :
     * <ul>
     * <li><b>format</b>          : string <i>Date format, default Y/m/d H:i is defined in bundle config</i></li>
     * <li><b>date_format</b>     : string <i>@see IntlDateformatter. Available values NONE, FULL, LONG, MEDIUM, SHORT</i></li>
     * <li><b>time_format</b>     : string <i>@see IntlDateformatter. Available values NONE, FULL, LONG, MEDIUM, SHORT</i></li>
     * <li><b>locale</b>          : string <i>Locale language for intl, default current locale @see \Locale::getDefault()</i></li>
     * </ul>
     * <p>Note: If format is set, intl (date_format, time_format) will be ignored</p>
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver, array $defaultOptions) {
        parent::setDefaultOptions($resolver, $defaultOptions);

        $resolver->setDefaults(array(
            'format' => null,
            'date_format' => $defaultOptions['date_format'],
            'time_format' => $defaultOptions['time_format'],
            'locale' => \Locale::getDefault()
        ));

        $resolver->addAllowedTypes(array(
            'format' => array('null', 'string'),
            'date_format' => 'string',
            'time_format' => 'string',
            'locale' => 'string'
        ));

        $resolver->addAllowedValues(array(
            'date_format' => array_keys(self::$formats),
            'time_format' => array_keys(self::$formats),
        ));
        
        $resolver->setNormalizers(array(
            'params' => function($options, array $params) {
        if (count($params) !== 1) {
            throw new \InvalidArgumentException('params must contains one param');
        }
        return $params;
    }
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected static function format($format, array $data, array $types = null) {
        $datetime = parent::format(null, $data);
        
        if (!$datetime instanceof \DateTime && $datetime !== null) {
            throw new \RuntimeException('$data must contains one element as a \Datetime');
        }

        return $datetime;
    }

    protected static function normalize($datetime, array $options) {
        assert($datetime instanceof \DateTime || $datetime === null);
        
        if ($datetime === null) {
            return null;
        }

        if (is_string($options['format'])) {
            return $datetime->format($options['format']);
        }
        
        $dateFormatter = new \IntlDateFormatter(
            $options['locale'],
            self::getIntlDateFormat($options['date_format']),
            self::getIntlDateFormat($options['time_format'])
        );
        return $dateFormatter->format($datetime);
    }
    
    private static function getIntlDateFormat($format) {
        if ( !isset(self::$formats[$format]) ) {
            throw new \InvalidArgumentException(sprintf('$format "%s" unkown. Available formats are (%s)',$format, implode(',', array_keys(self::$formats))));
        }
        return self::$formats[$format];
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'datetime';
    }

}
