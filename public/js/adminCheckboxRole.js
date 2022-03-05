function setRole(checkbox, role, userId) {
  checkbox.checked
    ? location.replace(`/admin/utilisateurs/setRole/${role}/add/${userId}`)
    : location.replace(`/admin/utilisateurs/setRole/${role}/remove/${userId}`);
}
