(module xhtmlfile mzscheme
  
(require
  (lib "ssax.ss" "ssax"))

(provide xhtmlfile)

(define (xhtmlfile path)
  (call-with-input-file path
    (lambda (p) #cs(SSAX:XML->SXML p '((#f . "http://www.w3.org/1999/xhtml")))))))
