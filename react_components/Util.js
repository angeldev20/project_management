import * as Constants from "./Constants";

export default class Util {
    static getDateStr(date) {
        return date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate();
    }

    static getDateHuman(date, withyear = false, withtime = false) {
        try {

            if (!(date instanceof Date))
                date = new Date(Date.parse(date));

            let date_str = Constants.ShortMonths[date.getMonth()] + " " + date.getDate() + (withyear ? (", " + date.getFullYear()) : "");

            return date_str;
        } catch (e) {
            return date;
        }
    }

    static getSlashedDate(date) {
        try {
            if (!(date instanceof Date))
                date = new Date(Date.parse(date));

            return (date.getMonth() + 1) + "/" + date.getDate() + "/" + date.getFullYear();
        } catch (e) {
            return date;
        }
    }
}