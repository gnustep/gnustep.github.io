(module sxmlout mzscheme
  
(require
  (lib "xml.ss" "xml"))

(provide sxmlout)

; SXML uses @ on the start of attribute lists.
; The XML library doesn't.  This converts the sxml ones into xexpr.
; It should become unnecessary once there is a good
; SXML serialiser, or the XML collection uses SXML too.
(define collapse-ats
  (lambda (tree)
    (cond
      ((and (list? tree) (not (null? tree)) (equal? '@ (car tree))) (cdr tree))
      ((list? tree) (map collapse-ats tree))
      (#t tree))))

(define (sxmlout sxml . port)
  (let ((outport (if (null? port) (current-output-port) (car port))))
    (display "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n" outport)
    (write-xml/content (xexpr->xml (collapse-ats sxml)) outport))))

;> (require "libs/sxmlout.ss")
;> (sxmlout '(html (body (p "yo"))))
