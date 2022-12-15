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
    if (data.email == "" || data.email == null) {
        message.email = "อีเมล์ไม่สามารถเว้นว่างได้";
    } else if (
        !/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(data.email)
    ) {
        message.email = "อีเมลไม่ถูกต้อง";
    }

    //VALIDATE BY ROLE
    if (data.role.indexOf("student") > -1) {
        //STUDENT ID
        if (data.std_id == "" || data.std_id == null) {
            message.std_id = "เลขประจำตัวไม่สามารถเว้นว่างได้";
        }

        //STUDENT CLASS
        if (data.class == "" || data.class == null) {
            message.class = "ระดับชั้นไม่สามารถเว้นว่างได้";
        } else if (
            isNaN(parseInt(data.class)) ||
            parseInt(data.class) < 1 ||
            parseInt(data.class > 6)
        ) {
            message.class = "ระดับชั้นไม่ถูกต้อง";
        }

        //STUDENT ROOM
        if (data.room == "" || data.room == null) {
            message.room = "ห้องไม่สามารถเว้นว่างได้";
        } else if (isNaN(parseInt(data.room)) || parseInt(data.class) < 1) {
            message.room = "ห้องเรียนไม่ถูกต้อง";
        }

        //STUDENT BIRTH DAY
        if (!isEmpty(data.birth_day) && isNaN(Date.parse(data.birth_day))) {
            message.birth_day = "วันเกิดไม่ถูกต้อง";
        }
    }

    return status(message);
}
