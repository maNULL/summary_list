import 'js-datepicker/dist/datepicker.min.css'
import datepicker from 'js-datepicker'

const options = {
  id: 1,
  alwaysShow: true,
  showAllDates: true,
  disableMobile: true,
  disableYearOverlay: true,
  startDay: 1,
  customDays: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
  customMonths: [
    'Январь',
    'Февраль',
    'Март',
    'Апрель',
    'Май',
    'Июнь',
    'Июль',
    'Август',
    'Сентябрь',
    'Октябрь',
    'Ноябрь',
    'Декабрь',
  ],
}

const startDate = datepicker('#summary-date-from', options)
const endDate = datepicker('#summary-date-to', options)

startDate.getRange()
endDate.getRange()

export { startDate, endDate }