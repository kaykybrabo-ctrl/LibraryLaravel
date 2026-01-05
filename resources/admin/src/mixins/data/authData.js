export default {
  data() {
    return {
      currentUser: null,
      authToken: '',
      showDropdown: false,
      routePage: 'home',
      successMessage: '',
      errorMessage: '',
      
      showRegister: false,
      showResetForm: false,
      resetEmail: '',
      resetCode: '',
      newPassword: '',
      newPasswordConfirm: '',
      resetPasswordVisible: false,
      resetPasswordConfirmVisible: false,
      
      loginForm: {
        email: '',
        password: '',
      },
      loginPasswordVisible: false,
      
      registerForm: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
      },
      registerPasswordVisible: false,
    };
  },
};
