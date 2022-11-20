//validationForm クラス と novalidate 属性を指定した form 要素を独自に検証
document.addEventListener('DOMContentLoaded', () => {
  const validationForm = document.getElementsByClassName('validationForm')[0];
  let validateAfterFirstSubmit = true;
  const errorClassName = 'error-js';

  if (validationForm) {
    const requiredElems = document.querySelectorAll('.required');
    const patternElems = document.querySelectorAll('.pattern');
    const equalToElems = document.querySelectorAll('.equal-to');
    const minlengthElems = document.querySelectorAll('.minlength');
    const maxlengthElems = document.querySelectorAll('.maxlength');
    const showCountElems = document.querySelectorAll('.showCount');

    const addError = (elem, className, defaultMessage) => {
      let errorMessage = defaultMessage;
      if (elem.hasAttribute('data-error-' + className)) {
        const dataError = elem.getAttribute('data-error-' + className);
        if (dataError) {
          errorMessage = dataError;
        }
      }
      if (!validateAfterFirstSubmit) {
        const errorSpan = document.createElement('span');
        errorSpan.classList.add(errorClassName, className);
        errorSpan.setAttribute('aria-live', 'polite');
        errorSpan.textContent = errorMessage;
        elem.parentNode.appendChild(errorSpan);
      }
    }

    const isValueMissing = (elem) => {
      if (elem.tagName === 'INPUT' && elem.getAttribute('type') === 'radio') {
        const className = 'required-radio';
        const errorSpan = elem.parentElement.querySelector('.' + errorClassName + '.' + className);
        const checkedRadio = elem.parentElement.querySelector('input[type="radio"]:checked');
        if (checkedRadio === null) {
          if (!errorSpan) {
            addError(elem, className, '選択は必須です。');
          }
          return true;
        } else {
          if (errorSpan) {
            elem.parentNode.removeChild(errorSpan);
          }
          return false;
        }
      } else if (elem.tagName === 'INPUT' && elem.getAttribute('type') === 'checkbox') {
        const className = 'required-checkbox';
        const errorSpan = elem.parentElement.querySelector('.' + errorClassName + '.' + className);
        const checkedCheckbox = elem.parentElement.querySelector('input[type="checkbox"]:checked');
        if (checkedCheckbox === null) {
          if (!errorSpan) {
            addError(elem, className, '選択は必須です。');
          }
          return true;
        } else {
          if (errorSpan) {
            elem.parentNode.removeChild(errorSpan);
          }
          return false;
        }
      } else {
        const className = 'required';
        const errorSpan = elem.parentElement.querySelector('.' + errorClassName + '.' + className);
        if (elem.value.trim().length === 0) {
          if (!errorSpan) {
            if (elem.tagName === 'SELECT') {
              addError(elem, className, '選択は必須です。');
            } else {
              addError(elem, className, '入力は必須です。');
            }
          }
          return true;
        } else {
          if (errorSpan) {
            elem.parentNode.removeChild(errorSpan);
          }
          return false;
        }
      }
    }

    requiredElems.forEach((elem) => {
      if (elem.tagName === 'INPUT' && (elem.getAttribute('type') === 'radio' || elem.getAttribute('type') === 'checkbox')) {
        const elems = elem.parentElement.querySelectorAll(elem.tagName);
        elems.forEach((elemsChild) => {
          elemsChild.addEventListener('change', () => {
            isValueMissing(elemsChild);
          });
        });
      } else {
        elem.addEventListener('input', () => {
          isValueMissing(elem);
        });
      }
    });

    const isPatternMismatch = (elem) => {
      const className = 'pattern';
      const attributeName = 'data-' + className;
      let pattern = new RegExp('^' + elem.getAttribute(attributeName) + '$');
      if (elem.getAttribute(attributeName) === 'email') {
        pattern = /^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ui;
      } else if (elem.getAttribute(attributeName) === 'tel') {
        pattern = /^\(?\d{2,5}\)?[-(\.\s]{0,2}\d{1,4}[-)\.\s]{0,2}\d{3,4}$/;
      }
      const errorSpan = elem.parentElement.querySelector('.' + errorClassName + '.' + className);
      if (elem.value.trim() !== '') {
        if (!pattern.test(elem.value)) {
          if (!errorSpan) {
            addError(elem, className, '入力された値が正しくないようです。');
          }
          return true;
        } else {
          if (errorSpan) {
            elem.parentNode.removeChild(errorSpan);
          }
          return false;
        }
      } else if (elem.value === '' && errorSpan) {
        elem.parentNode.removeChild(errorSpan);
      }
    }

    patternElems.forEach((elem) => {
      elem.addEventListener('input', () => {
        isPatternMismatch(elem);
      });
    });

    const isNotEqualTo = (elem) => {
      const className = 'equal-to';
      const attributeName = 'data-' + className;
      const equalTo = elem.getAttribute(attributeName);
      const equalToElem = document.getElementById(equalTo);
      const errorSpan = elem.parentElement.querySelector('.' + errorClassName + '.' + className);
      if (elem.value.trim() !== '' && equalToElem.value.trim() !== '') {
        if (equalToElem.value !== elem.value) {
          if (!errorSpan) {
            addError(elem, className, '入力された値が一致しません。');
          }
          return true;
        } else {
          if (errorSpan) {
            elem.parentNode.removeChild(errorSpan);
          }
          return false;
        }
      }
    }

    equalToElems.forEach((elem) => {
      elem.addEventListener('input', () => {
        isNotEqualTo(elem);
      });
      const compareTarget = document.getElementById(elem.getAttribute('data-equal-to'));
      if (compareTarget) {
        compareTarget.addEventListener('input', () => {
          isNotEqualTo(elem);
        });
      }
    });

    const getValueLength = (value) => {
      return (value.match(/[\uD800-\uDBFF][\uDC00-\uDFFF]|[\s\S]/g) || []).length;
    }

    const isTooShort = (elem) => {
      const className = 'minlength';
      const attributeName = 'data-' + className;
      const minlength = elem.getAttribute(attributeName);
      const errorSpan = elem.parentElement.querySelector('.' + errorClassName + '.' + className);
      if (elem.value !== '') {
        const valueLength = getValueLength(elem.value);
        if (valueLength < minlength) {
          if (!errorSpan) {
            addError(elem, className, minlength + '文字以上で入力ください。');
          }
          return true;
        } else {
          if (errorSpan) {
            elem.parentNode.removeChild(errorSpan);
          }
          return false;
        }
      } else if (elem.value === '' && errorSpan) {
        elem.parentNode.removeChild(errorSpan);
      }
    }

    minlengthElems.forEach((elem) => {
      elem.addEventListener('input', () => {
        isTooShort(elem);
      });
    });

    const isTooLong = (elem) => {
      const className = 'maxlength';
      const attributeName = 'data-' + className;
      const maxlength = elem.getAttribute(attributeName);
      const errorSpan = elem.parentElement.querySelector('.' + errorClassName + '.' + className);
      if (elem.value !== '') {
        const valueLength = getValueLength(elem.value);
        if (valueLength > maxlength) {
          if (!errorSpan) {
            addError(elem, className, maxlength + '文字以内で入力ください。');
          }
          return true;
        } else {
          if (errorSpan) {
            elem.parentNode.removeChild(errorSpan);
          }
          return false;
        }
      } else if (elem.value === '' && errorSpan) {
        elem.parentNode.removeChild(errorSpan);
      }
    }

    maxlengthElems.forEach((elem) => {
      elem.addEventListener('input', () => {
        isTooLong(elem);
      });
    });

    showCountElems.forEach((elem) => {
      const dataMaxlength = elem.getAttribute('data-maxlength');
      if (dataMaxlength && !isNaN(dataMaxlength)) {
        const countElem = document.createElement('p');
        countElem.classList.add('countSpanWrapper');
        countElem.innerHTML = '<span class="countSpan">0</span>/' + parseInt(dataMaxlength);
        elem.parentNode.appendChild(countElem);
      }
      elem.addEventListener('input', (e) => {
        const countSpan = elem.parentElement.querySelector('.countSpan');
        if (countSpan) {
          const count = getValueLength(e.currentTarget.value);
          countSpan.textContent = count;
          if (count > dataMaxlength) {
            countSpan.style.setProperty('color', 'red');
            countSpan.classList.add('overMaxCount');
          } else {
            countSpan.style.removeProperty('color');
            countSpan.classList.remove('overMaxCount');
          }
        }
      });
    });

    validationForm.addEventListener('submit', (e) => {
      validateAfterFirstSubmit = false;
      requiredElems.forEach((elem) => {
        if (isValueMissing(elem)) {
          e.preventDefault();
        }
      });
      patternElems.forEach((elem) => {
        if (isPatternMismatch(elem)) {
          e.preventDefault();
        }
      });
      minlengthElems.forEach((elem) => {
        if (isTooShort(elem)) {
          e.preventDefault();
        }
      });
      maxlengthElems.forEach((elem) => {
        if (isTooLong(elem)) {
          e.preventDefault();
        }
      });
      equalToElems.forEach((elem) => {
        if (isNotEqualTo(elem)) {
          e.preventDefault();
        }
      });

      const errorElem = document.querySelector('.' + errorClassName);
      if (errorElem) {
        const errorElemOffsetTop = errorElem.offsetTop;
        window.scrollTo({
          top: errorElemOffsetTop - 40,
          behavior: 'smooth'
        });
      }
    });
  }
});