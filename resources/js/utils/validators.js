export const checkDate = (value) => {
    //*usage as html attribute> :rules="[validateNotificationDate]"
    if (!value) return 'Date is required';

    const [year, month, day] = value.split('/');

    // چک کردن چهار رقمی بودن سال
    if (year.length !== 4 || isNaN(year)) {
        return 'Year must be 4 digits';
    }

    // تبدیل ماه و روز به دو رقمی
    const formattedMonth = month.padStart(2, '0');
    const formattedDay = day.padStart(2, '0');

    // بازسازی تاریخ
    const formattedDate = `${year}/${formattedMonth}/${formattedDay}`;

    // چک کردن معتبر بودن تاریخ
    const date = new Date(formattedDate);
    if (isNaN(date)) {
        return 'Invalid date';
    }

    return true; // معتبر
};