export function isStudent(role) {
    if (role.indexOf("student") > -1) return true;
    return false;
}

export function isTeacher(role) {
    if (role.indexOf("teacher") > -1) return true;
    return false;
}

export function isAdmin(role) {
    if (role.indexOf("admin") > -1) return true;
    return false;
}

export function isSystem(role) {
    if (role.indexOf("system") > -1) return true;
    return false;
}
