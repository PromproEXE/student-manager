function status(message) {
    return {
        status:
            Object.keys(message).length == 0 || message == null ? true : false,
        message: message.length != 0 || message != null ? message : {},
    };
}

function isEmpty(str) {
    return !str || str.length === 0;
}

export function validateUser(data) {
    let message = {};
    //USER NAME
    if (data.name == "" || data.name == null) {
        message.name = "ชื่อ-นามสกุลไม่สามารถเป็นเว้นว่างได้";
    }

    //USER EMAIL
    // if (data.email == "" || data.email == null) {
    //     message.email = "อีเมล์ไม่สามารถเว้นว่างได้";
    // } else if (
    //     !/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(data.email)
    // ) {
    //     message.email = "อีเมลไม่ถูกต้อง";
    // }

    //VALIDATE BY ROLE
    if (data.role.indexOf("student") > -1) {
        //STUDENT ID
        if (isEmpty(data.std_id)) {
            message.std_id = "เลขประจำตัวไม่สามารถเว้นว่างได้";
        }

        //STUDENT CLASS
        if (isEmpty(data.class)) {
            message.class = "ระดับชั้นไม่สามารถเว้นว่างได้";
        } else if (
            isNaN(parseInt(data.class)) ||
            parseInt(data.class) < 1 ||
            parseInt(data.class) > 6
        ) {
            message.class = "ระดับชั้นไม่ถูกต้อง";
        }

        //STUDENT ROOM
        if (isEmpty(data.room)) {
            message.room = "ห้องไม่สามารถเว้นว่างได้";
        } else if (isNaN(parseInt(data.room)) || parseInt(data.class) < 1) {
            message.room = "ห้องเรียนไม่ถูกต้อง";
        }

        //STUDENT BIRTH DAY
        if (!isEmpty(data.birth_day) && isNaN(Date.parse(data.birth_day))) {
            message.birth_day = "วันเกิดไม่ถูกต้อง";
        }
    }

    if (data.role.indexOf("teacher") > -1) {
        //TEACHER ENGLISH NAME
        if (isEmpty(data.eng_name)) {
            message.eng_name =
                "ชื่อ-นามสกุล (ภาษาอังกฤษ) ไม่สามารถเป็นเว้นว่างได้";
        }

        //TEACHER CLASS
        if (isEmpty(data.class)) {
            message.class = "ระดับชั้นไม่สามารถเว้นว่างได้";
        } else if (
            isNaN(parseInt(data.class)) ||
            parseInt(data.class) < 1 ||
            parseInt(data.class) > 6
        ) {
            message.class = "ระดับชั้นไม่ถูกต้อง";
        }

        //TEACHER DEPARTMENT
        if (isEmpty(data.data.department)) {
            message.department = "กรุณาเลือกกลุ่มสาระการเรียนรู้";
        }

        //TEACHER ADVISOR CLASS
        if (
            !isEmpty(data.data.advisor.class) &&
            (data.data.advisor.class < 1 || data.data.advisor.class > 6)
        ) {
            message.advisor_class = "ระดับชั้นไม่ถูกต้อง";
        }

        //TEACHER ADVISOR ROOM
        if (!isEmpty(data.data.advisor.room) && data.data.advisor.room < 1) {
            message.advisor_room = "ห้องไม่ถูกต้อง";
        }
    }

    if (data.role.indexOf("admin") > -1) {
        //OFFICER ENGLISH NAME
        if (isEmpty(data.eng_name)) {
            message.eng_name =
                "ชื่อ-นามสกุล (ภาษาอังกฤษ) ไม่สามารถเป็นเว้นว่างได้";
        }
    }

    return status(message);
}

export function validateAbsent(data) {
    let messageBag = {};

    //ABSENT FROM
    if (isEmpty(data.from)) {
        messageBag.from = "วันลาตั้งแต่ไม่สามารถเว้นว่างได้";
    } else if (isNaN(Date.parse(data.from))) {
        messageBag.from = "วันลาตั้งแต่ไม่ถูกต้อง";
    }

    //ABSENT TO
    if (isEmpty(data.to)) {
        messageBag.to = "วันลาสิ้นสุดไม่สามารถเว้นว่างได้";
    } else if (isNaN(Date.parse(data.to))) {
        messageBag.to = "วันลาสิ้นสุดไม่ถูกต้อง";
    } else if (new Date(data.from).getTime() < new Date(data.to).getTime()) {
        messageBag.to = "วันลาสิ้นสุดไม่สามารถเป็นวันก่อนวันลาตั้งแต่ได้";
    }

    //ABSENT TYPE
    if (isEmpty(data.type)) {
        messageBag.type = "ประเภทการลาไม่สามารถเว้นว่างได้";
    }

    //ABSENT DETAILS
    if (isEmpty(data.details)) {
        messageBag.details = "รายละเอียดการลาไม่สามารถเว้นว่างได้";
    }

    return status(messageBag);
}
