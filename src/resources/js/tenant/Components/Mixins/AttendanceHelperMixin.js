import MemoizeMixins from "../../../common/Helper/Support/MemoizeMixins";
import moment from "moment";
import {differentInTime, serverDateFormat} from "../../../common/Helper/Support/DateTimeHelper";

export default {
    mixins:[MemoizeMixins],
    methods: {
        getDetails(working_shift, date) {
            return this.memoize(`details-${working_shift.id}-${date.format('ddd').toLowerCase()}`, () => {
                return working_shift.details.find(details => {
                    return String(date.locale('en').format('ddd')).toLowerCase() === details.weekday;
                })
            })
        },
        getEmployeeWorkingShiftFromDate(working_shifts, date, defaultWorkShift) {
            if (!working_shifts.length) {
                return  defaultWorkShift;
            }

            return working_shifts.find(working_shift => {
                const start_at = moment(working_shift.pivot.start_date, serverDateFormat);
                const end_at = working_shift.pivot?.end_date ? moment(working_shift.pivot.end_date, serverDateFormat): null;

                if ((date.isSame(start_at) || date.isAfter(start_at)) && !working_shift.pivot.end_date) {
                    return true;
                }

                if (working_shift.pivot.end_date && (date.isBetween(start_at, end_at) || date.isSame(start_at)) && date.isBefore(end_at)) {
                    return true;
                }

            }) || defaultWorkShift;
        },
        getTotalWorked(attendance) {
            // console.log(this.getTotalWorkedDuration(attendance.details));

            return this.memoize(`attendance-${attendance.id}`, () => {
                return this.getTotalWorkedDuration(attendance.details)
            });
        },
        getTotalWorkedDuration(details) {
            return details.reduce((carry, details) => {
                return carry.add(moment.duration(differentInTime(details.in_time, details.out_time, true)))
            }, moment.duration(0));
        },

// hassan
        getTotalWorked2(attendance) {
            // console.log(this.getTotalWorkedDuration2(attendance.details));
            //TODO: fix this function to match the excel export

            return this.memoize(`attendance1-${attendance.id}`, () => {
                let value =  this.getTotalWorkedDuration2(attendance.details);
                // console.log(value);
                let correction = attendance.hours_correction > 0 ? attendance.hours_correction : 0;
                let minutes_correction = attendance.minutes_correction > 0 ? attendance.minutes_correction : 0;
                console.log('posetive: '+correction);
                let correction_negative = attendance.hours_correction < 0 ? attendance.hours_correction : 0;
                let minutes_correction_negative = attendance.minutes_correction < 0 ? attendance.minutes_correction : 0;
                correction_negative = Math.abs(correction_negative);
                minutes_correction_negative = Math.abs(minutes_correction_negative);
                console.log('negative: '+correction_negative);
                let subtract = 0;
                let lunch_in = 0;
                try {
                    lunch_in = attendance.details[0].project.lunch_in; //get the first lunch zone if ths employee attended more than one projects
                } catch (e) {

                }
                // console.log(lunch_in);
                 subtract = lunch_in === 0 ? 0 : 1 ; //check if the lunch zone is inside the project area or outside
                return moment.duration(differentInTime(value[0], value[1], true))
                    .subtract({hours:subtract+correction_negative, minute:minutes_correction_negative})
                    .subtract({minute:value[2]})
                    .add({hours:correction,minute:minutes_correction})
            });
        },
        getTotalWorkedDuration2(details) {
            let f_in = []; //all in records
            // console.log(f_in);
            let l_out = []; //all out records
            // console.log(l_out);

            let r_in = f_in.reverse(); //reversed
            let r_out = l_out.reverse(); //reversed
            // console.log(differentInTime(f_in[0], l_out[l_out.length-1]));
             details.reduce((carry, details) => { //fill the arrays
                // console.log(details.in_time);
                f_in.push(details.in_time);
                l_out.push(details.out_time);

                // return carry.add(moment.duration(differentInTime(f_in[0], l_out[l_out.length-1], true)))
            }, moment.duration(0));
             // return differentInTime(f_in[0], l_out[l_out.length-1]);
            let breakTime = 0;
            for (let i = 0;i < f_in.length; i++) { //create a loop with the size of records
                // console.log(i);
                let breakDuration = moment.duration(differentInTime(r_out[i+1], r_in[i], true)).asMinutes(); //calculate the break duration between visits
                if ( f_in.length > 1 ){ //if multi attendance
                    if ( breakDuration > 120) { // if the time spend outside the project is
                        console.log(breakDuration)
                        breakTime = breakTime + moment.duration(differentInTime(r_out[i+1], r_in[i], true)).asMinutes(); //increment the breaktime

                        i= i+1;

                    } else {

                    }
                } else {

                }
            }
            // console.log(br_in);
            // console.log(br_out);
            //
            // console.log(gd_in);
            // console.log(gd_out);


            return [f_in[f_in.length-1], l_out[0], breakTime]; //return array with first/last out record and the breaktime

        },

        //hassan
        getProjects(attendance) {
            // return attendance.details[0].project_id;
            // const projectsarray = Object.entries(attendance.details).map((arr) => ({
            //     fieldName: arr[0],
            //     message: arr[1],
            // }));
            // console.log(projectsarray);



            let projectName = '';
            let arrProjectN=[];
            try {
            attendance.details.forEach((element, index) => {
                if (element.project_id !== null) {
                    // projectName = index > 0 ? ', ' + element.project_id : +element.project_id;
                    // projectName = index > 0 ? ', ' + element.project_id : +element.project_id;

                    // projectName =  (projectName == null) ? element.project.name : element.project.name + ', '+projectName;
                    (projectName == null) ? arrProjectN.push(element.project.pme_id) : arrProjectN.push(element.project.pme_id);


                    // console.log(projectName);

                    // console.log('index: '+index+ 'element: '+element.project_id+' || '+projectName);

                }



            });
        } catch
            (e)
            {

            }
            // console.log(projectName);
            arrProjectN = [...new Set(arrProjectN)];

            try {
                arrProjectN.forEach((element, index) => {
                        projectName =  (projectName == null) ? element : element + ', '+projectName;

                });

            }catch (e) {

            }

            // console.log(arrProjectN);
            projectName = projectName ? projectName : '-';

            return projectName;


        },
        getLunchZone(attendance) {
            let break_time = 0;

            try {
                let lunch_in  = attendance.details[0].project.lunch_in;
                console.log(lunch_in);
                if (lunch_in === 1) {
                    break_time = attendance.details[0].project.working_shifts[0].lunch_break;
                }
            }catch (e) {

            }
            return break_time;
        },
        getCorrection(attendance) {
            let hours = attendance.hours_correction*3600;
            let minutes = attendance.minutes_correction*60;

                // let correction  = moment().startOf('day')
                //     .seconds(hours+minutes)
                //     .format('H:mm');
            let correction  = (Math.round((hours+minutes)/3600 * 100) / 100).toFixed(2);

            return correction;
        },
        getAllProjects(details_project) {
            return details_project.reduce((carry, details_project) => {
                return carry.add(details_project.project_id)
            }, moment.duration(0));
        }
    }
}
