import moment from 'moment';

const langs = {
    dutch: 'nl',
    english: 'en',
    french: 'fr',
    german: 'de',
    italian: 'it',
    norwegian: 'no',
    polish: 'pl',
    portuguese: 'pt',
    russian: 'ru',
    spanish: 'es',
    turkish: 'tr'
};

const lang = Cookies.get('fc2language');
moment.locale(langs[lang]);